<?php
/**
 * com_mailer class.
 *
 * @package Components\mailer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_mailer main class.
 *
 * @package Components\mailer
 */
class com_mailer extends component {
	/**
	 * A cache of the included mails.php files.
	 * @var array
	 * @access private
	 */
	private $mail_include_cache = array();
	/**
	 * A (readonly) SQLite database connection for the unsubscribed DB.
	 * @var mixed
	 * @access private
	 */
	private $db;

	/**
	 * Get a mail's definition array.
	 * @param array $mail The mail entry array.
	 * @return array The mail definition.
	 */
	public function get_mail_def($mail) {
		$component = clean_filename($mail['component']);
		$name = $mail['mail'];
		if (isset($this->mail_include_cache[$component])) {
			$include = $this->mail_include_cache[$component];
		} else {
			if (!file_exists("components/$component/mails.php"))
				return null;
			$include = include("components/$component/mails.php");
			$this->mail_include_cache[$component] = $include;
		}
		return $include[$name];
	}

	/**
	 * Creates and attaches a module which lists renditions.
	 * @return module The module.
	 */
	public function list_renditions() {
		global $_;

		$module = new module('com_mailer', 'rendition/list', 'content');

		$module->renditions = $_->nymph->getEntities(
				array('class' => com_mailer_rendition),
				array('&',
					'tag' => array('com_mailer', 'rendition')
				)
			);

		if ( empty($module->renditions) )
			pines_notice('No renditions found.');

		return $module;
	}

	/**
	 * Creates and attaches a module which lists templates.
	 * @return module The module.
	 */
	public function list_templates() {
		global $_;

		$module = new module('com_mailer', 'template/list', 'content');

		$module->templates = $_->nymph->getEntities(
				array('class' => com_mailer_template),
				array('&',
					'tag' => array('com_mailer', 'template')
				)
			);

		if ( empty($module->templates) )
			pines_notice('No templates found.');

		return $module;
	}

	/**
	 * Get an array of all the mail types.
	 * 
	 * Goes through each component's mails.php file.
	 *
	 * @return array Mail types.
	 */
	public function mail_types() {
		global $_;
		$return = array();
		foreach ($_->components as $cur_component) {
			if (strpos($cur_component, 'tpl_') === 0)
				continue;
			if (!file_exists("components/$cur_component/mails.php"))
				continue;
			$mails = include("components/$cur_component/mails.php");
			if (!$mails || (array) $mails !== $mails)
				continue;
			if ($mails)
				$return[$cur_component] = $mails;
		}
		return $return;
	}

	/**
	 * Add an email address to the unsubscribed DB.
	 *
	 * @param string $email The email address to add.
	 * @return bool True on success, false on failure, 0 if the database hasn't been set up.
	 */
	public function unsubscribe_add($email) {
		global $_;
		// Validate and lowercase email address.
		if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email))
			return false;
		$email = strtolower($email);

		// Get the DB.
		$filename = $_->config->com_mailer->unsubscribe_db;
		if (!$filename) {
			pines_log('Unsubscribed user database has not been set up yet. Please edit the config for com_mailer.', 'error');
			return 0;
		}
		if (file_exists($filename) && !is_readable($filename)) {
			pines_log('Unsubscribed user database file cannot be read!', 'error');
			return false;
		}
		// Build the queries.
		$create_table = 'CREATE TABLE IF NOT EXISTS "unsubscribed" ("email" text NOT NULL UNIQUE);';
		$insert_email = 'INSERT OR REPLACE INTO "unsubscribed" VALUES ("'.$email.'");';
		// Now run the SQL.
		if (class_exists('SQLite3')) {
			$db = new SQLite3($filename);
			if (!$db->query($create_table)) {
				pines_error("SQL Create Table error: ".$db->lastErrorMsg());
				return false;
			}
			if (!$db->query($insert_email)) {
				pines_error("SQL Insert error: ".$db->lastErrorMsg());
				return false;
			}
			$id = $db->lastInsertRowID();
			if (!$id && $id !== 0) {
				pines_error("SQL Insert error: ".$db->lastErrorMsg());
				return false;
			}
			$db->close();
		} elseif (function_exists('sqlite_open')) {
			$db = sqlite_open($filename);
			if (!sqlite_query($db, $create_table, SQLITE_NUM, $error)) {
				pines_error("SQL Create Table error: $error");
				return false;
			}
			if (!sqlite_query($db, $insert_email, SQLITE_NUM, $error)) {
				pines_error("SQL Insert error: $error");
				return false;
			}
			$id = sqlite_last_insert_rowid($db);
			if (!$id && $id !== 0) {
				pines_error("SQL Insert error: $error");
				return false;
			}
			sqlite_close($db);
		} else {
			pines_log('SQLite is not available! Please install the SQLite PHP extension.', 'error');
			pines_error('SQLite is not available! Please install the SQLite PHP extension.');
			return false;
		}
		return true;
	}

	/**
	 * Determine if an email address is unsubscribed.
	 * 
	 * Returning false if the database hasn't been set up allows emails to be
	 * sent without an unsubscribe DB. Returning true on error ensures no emails
	 * are sent to unsubscribed users if the DB can't be queried.
	 *
	 * @param string $email The email address in question.
	 * @return bool True if the address is unsubscribed or on failure, false if it isn't or the database hasn't been set up.
	 */
	public function unsubscribe_query($email) {
		global $_;
		// Validate and lowercase email address.
		if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email))
			return false;
		$email = strtolower($email);

		// Get the DB.
		$filename = $_->config->com_mailer->unsubscribe_db;
		if (!file_exists($filename) || !is_readable($filename)) {
			pines_log('Unsubscribed user database has not been set up yet. Please edit the config for com_mailer.', 'error');
			return false;
		}
		// Build the queries.
		$select_email = 'SELECT * FROM "unsubscribed" WHERE email="'.$email.'";';
		// Now run the SQL.
		if (class_exists('SQLite3')) {
			if (!$this->db)
				$this->db = new SQLite3($filename, SQLITE3_OPEN_READONLY);
			if (($result = $this->db->query($select_email)) === false) {
				pines_error("SQL Query error: ".$this->db->lastErrorMsg());
				return true;
			}
			$row = $result->fetchArray();
		} elseif (function_exists('sqlite_open')) {
			if (!$this->db)
				$this->db = sqlite_open($filename);
			if (($result = sqlite_query($this->db, $select_email, SQLITE_NUM, $error)) === false) {
				pines_error("SQL Query error: $error");
				return true;
			}
			$row = sqlite_fetch_array($result, SQLITE_NUM);
		} else {
			pines_log('SQLite is not available! Please install the SQLite PHP extension.', 'error');
			pines_error('SQLite is not available! Please install the SQLite PHP extension.');
			return false;
		}
		return ($email == $row[0]);
	}

	/**
	 * Remove an email address from the unsubscribed DB.
	 *
	 * @param string $email The email address to remove.
	 * @return bool True on success, false on failure.
	 */
	public function unsubscribe_remove($email) {
		global $_;
		// Validate and lowercase email address.
		if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email))
			return false;
		$email = strtolower($email);

		// Get the DB.
		$filename = $_->config->com_mailer->unsubscribe_db;
		if (!file_exists($filename) || !is_readable($filename)) {
			pines_log('Unsubscribed user database has not been set up yet. Please edit the config for com_mailer.', 'error');
			return false;
		}
		// Build the queries.
		$delete_email = 'DELETE FROM "unsubscribed" WHERE email="'.$email.'";';
		// Now run the SQL.
		if (class_exists('SQLite3')) {
			$db = new SQLite3($filename);
			if (!$db->query($delete_email)) {
				pines_error("SQL Delete error: ".$db->lastErrorMsg());
				return false;
			}
			$db->close();
		} elseif (function_exists('sqlite_open')) {
			$db = sqlite_open($filename);
			if (!sqlite_query($db, $delete_email, SQLITE_NUM, $error)) {
				pines_error("SQL Delete error: $error");
				return false;
			}
			sqlite_close($db);
		} else {
			pines_log('SQLite is not available! Please install the SQLite PHP extension.', 'error');
			pines_error('SQLite is not available! Please install the SQLite PHP extension.');
			return false;
		}
		return true;
	}

	/**
	 * Send a system registered email.
	 * @param array|string $mail The mail entry array, or a string representation ("com_example/save_foobar").
	 * @param array $macros An associative array of the macros available for the email. They have to be the same as in the mail definition. Remember to use htmlspecialchars!
	 * @param mixed $recipient A user, group, customer, employee, etc. that has user/group info and an email address.
	 * @param bool $send If this is set to false, the com_mailer_mail object is returned before being sent. Allows for adding attachments, etc.
	 * @return bool|com_mailer_mail True on success, false on failure. If $send is false, returns the mail instead.
	 */
	public function send_mail($mail, $macros = array(), $recipient = null, $send = true) {
		global $_;
		if ((array) $mail !== $mail) {
			list($component, $defname) = explode('/', $mail, 2);
			$mail = array(
				'component' => $component,
				'mail' => $defname
			);
		}
		$def = $this->get_mail_def($mail);
		if (!$def)
			return false;

		$from = $_->config->com_mailer->from_address;

		// Format recipient.
		if ($recipient && is_string($recipient))
			$recipient = (object) array('email' => $recipient);

		// Find any renditions.
		$renditions = (array) $_->nymph->getEntities(
				array('class' => com_mailer_rendition),
				array('&',
					'tag' => array('com_mailer', 'rendition'),
					'strict' => array(
						array('enabled', true),
						array('type', "{$mail['component']}/{$mail['mail']}")
					)
				)
			);
		$rendition = null;
		foreach ($renditions as $cur_rendition) {
			if ($cur_rendition->ready()) {
				$rendition = $cur_rendition;
				break;
			}
		}
		unset($renditions, $cur_rendition);

		// Get the email recipient(s).
		if ($rendition) {
			if (!empty($rendition->from))
				$from = $rendition->from;
			if (!$recipient) {
				// If it's supposed to have a recipient already, report failure.
				if ($def['has_recipient'])
					return false;
				if ($rendition->to) {
					if (strpos($rendition->to, ',') === false) {
						if (preg_match('/<.+@.+>/', $rendition->to))
							$check_email = trim(preg_replace('/^.*<(.+@.+)>.*$/', '$1', $rendition->to));
						else
							$check_email = trim($rendition->to);
						// Check for a user or group with that email.
						$user = $_->nymph->getEntity(
								array('class' => user),
								array('&',
									'tag' => array('com_user', 'user'),
									'strict' => array('email', $check_email)
								)
							);
						if ($user)
							$recipient = $user;
						else {
							$group = $_->nymph->getEntity(
									array('class' => group),
									array('&',
										'tag' => array('com_user', 'group'),
										'strict' => array('email', $check_email)
									)
								);
							if ($group)
								$recipient = $group;
						}
					}
					if (!$recipient)
						$recipient = (object) array('email' => $rendition->to);
				} else {
					// Send to the master address if there's no recipient.
					if (!$_->config->com_mailer->master_address)
						return false;
					$recipient = (object) array('email' => $_->config->com_mailer->master_address);
				}
			}
		} elseif (!$recipient) {
			if ($def['has_recipient'] || !$_->config->com_mailer->master_address)
				return false;
			$recipient = (object) array('email' => $_->config->com_mailer->master_address);
		}

		// Remove emails that are on the unsubscribed list if the definition
		// obeys it.
		if ($def['unsubscribe']) {
			if (strpos($recipient->email, ',') !== false)
				$emails = explode(',', $recipient->email);
			else
				$emails = array($recipient->email);

			$changed = false;
			foreach ($emails as $key => &$cur_email) {
				$cur_email = trim($cur_email);
				if (preg_match('/<.+@.+>/', $cur_email))
					$check_email = trim(preg_replace('/^.*<(.+@.+)>.*$/', '$1', $cur_email));
				else
					$check_email = $cur_email;
				if ($this->unsubscribe_query($check_email)) {
					unset($emails[$key]);
					$changed = true;
				}
			}
			unset($cur_email);

			if ($changed)
				$recipient->email = implode(', ', $emails);
			// If every user is unsubscribed, report a success without sending
			// an email.
			if (!$recipient->email && $send)
				return true;
		}

		// Get the email contents.
		$body = array();
		if ($rendition) {
			$body['subject'] = $rendition->subject;
			$body['content'] = $rendition->content;
		} else {
			$view = $def['view'];
			$view_callback = $def['view_callback'];
			if (!isset($view) && !isset($view_callback))
				return false;

			// Make a module from the view.
			if (isset($view))
				$module = new module($component, $view);
			else {
				$module = call_user_func($view_callback, null, null, $options);
				if (!$module)
					return false;
			}

			// The contents of the module become the body of the email.
			$body['content'] = $module->render();
			$body['subject'] = $module->title;
		}

		// Get the template.
		$templates = (array) $_->nymph->getEntities(
				array('class' => com_mailer_template),
				array('&',
					'tag' => array('com_mailer', 'template'),
					'strict' => array('enabled', true)
				)
			);
		$template = null;
		// Get the first template that's ready.
		foreach ($templates as $cur_template) {
			if ($cur_template->ready()) {
				$template = $cur_template;
				break;
			}
		}
		unset($templates, $cur_template);
		// If there is no template, use a default one.
		if (!$template)
			$template = com_mailer_template::factory();

		// Build the body of the email.
		$body['content'] = str_replace('#content#', $body['content'], str_replace('#content#', $template->content, $template->document));

		// Protects users from being unsubscribed by anyone.
		$unsubscribe_secret = md5($recipient->email.$_->config->com_mailer->unsubscribe_key);
		$unsubscribe_url = pines_url('com_mailer', 'unsubscribe', array('email' => $recipient->email, 'verify' => $unsubscribe_secret), true);

		// Replace macros and search strings.
		foreach ($body as &$cur_field) {
			foreach ((array) $template->replacements as $cur_string) {
				if (!$cur_string['macros'])
					continue;
				if (strpos($cur_field, $cur_string['search']) !== false)
					$cur_field = str_replace($cur_string['search'], $cur_string['replace'], $cur_field);
			}
			if (strpos($cur_field, '#subject#') !== false)
				$cur_field = str_replace('#subject#', h($body['subject']), $cur_field);
			// Links
			if (strpos($cur_field, '#site_link#') !== false)
				$cur_field = str_replace('#site_link#', h($_->config->full_location), $cur_field);
			if (strpos($cur_field, '#unsubscribe_link#') !== false)
				$cur_field = str_replace('#unsubscribe_link#', h($unsubscribe_url), $cur_field);
			// Recipient
			if (strpos($cur_field, '#to_username#') !== false)
				$cur_field = str_replace('#to_username#', h($recipient->username ? $recipient->username : $recipient->groupname), $cur_field);
			if (strpos($cur_field, '#to_name#') !== false)
				$cur_field = str_replace('#to_name#', h($recipient->name), $cur_field);
			if (strpos($cur_field, '#to_first_name#') !== false)
				$cur_field = str_replace('#to_first_name#', h($recipient->name_first), $cur_field);
			if (strpos($cur_field, '#to_last_name#') !== false)
				$cur_field = str_replace('#to_last_name#', h($recipient->name_last), $cur_field);
			if (strpos($cur_field, '#to_email#') !== false)
				$cur_field = str_replace('#to_email#', h($recipient->email), $cur_field);
			// Current User
			if (strpos($cur_field, '#username#') !== false)
				$cur_field = str_replace('#username#', h($_SESSION['user']->username), $cur_field);
			if (strpos($cur_field, '#name#') !== false)
				$cur_field = str_replace('#name#', h($_SESSION['user']->name), $cur_field);
			if (strpos($cur_field, '#first_name#') !== false)
				$cur_field = str_replace('#first_name#', h($_SESSION['user']->name_first), $cur_field);
			if (strpos($cur_field, '#last_name#') !== false)
				$cur_field = str_replace('#last_name#', h($_SESSION['user']->name_last), $cur_field);
			if (strpos($cur_field, '#email#') !== false)
				$cur_field = str_replace('#email#', h($_SESSION['user']->email), $cur_field);
			// Date/Time
			if (strpos($cur_field, '#date_short#') !== false)
				$cur_field = str_replace('#date_short#', h(format_date(time(), 'date_short')), $cur_field);
			if (strpos($cur_field, '#date_med#') !== false)
				$cur_field = str_replace('#date_med#', h(format_date(time(), 'date_med')), $cur_field);
			if (strpos($cur_field, '#date_long#') !== false)
				$cur_field = str_replace('#date_long#', h(format_date(time(), 'date_long')), $cur_field);
			if (strpos($cur_field, '#time_short#') !== false)
				$cur_field = str_replace('#time_short#', h(format_date(time(), 'time_short')), $cur_field);
			if (strpos($cur_field, '#time_med#') !== false)
				$cur_field = str_replace('#time_med#', h(format_date(time(), 'time_med')), $cur_field);
			if (strpos($cur_field, '#time_long#') !== false)
				$cur_field = str_replace('#time_long#', h(format_date(time(), 'time_long')), $cur_field);
			// System
			if (strpos($cur_field, '#system_name#') !== false)
				$cur_field = str_replace('#system_name#', h($_->config->system_name), $cur_field);
			if (strpos($cur_field, '#page_title#') !== false)
				$cur_field = str_replace('#page_title#', h($_->config->page_title), $cur_field);
			// Definition Macros
			foreach ($def['macros'] as $cur_name => $cur_desc) {
				if (isset($macros[$cur_name]) && strpos($cur_field, "#$cur_name#") !== false)
					$cur_field = str_replace("#$cur_name#", $macros[$cur_name], $cur_field);
			}
			foreach ((array) $template->replacements as $cur_string) {
				if ($cur_string['macros'])
					continue;
				if (strpos($cur_field, $cur_string['search']) !== false)
					$cur_field = str_replace($cur_string['search'], $cur_string['replace'], $cur_field);
			}
		}
		unset($cur_field);

		// Build the mail object.
		$email = com_mailer_mail::factory($from, isset($recipient->name) ? "\"".str_replace('"', '', $recipient->name)."\" <{$recipient->email}>" : $recipient->email, $body['subject'], $body['content']);
		if ($rendition) {
			if ($rendition->cc)
				$email->addHeader('CC', $rendition->cc);
			if ($rendition->bcc)
				$email->addHeader('BCC', $rendition->bcc);
		}

		// Now finish up.
		if ($send)
			return $email->send();
		return $email;
	}
}