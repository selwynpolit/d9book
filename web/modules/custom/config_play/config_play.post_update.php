<?php

/**
 * Update "Contact Us" form to have a reply message.
 */
function config_play_post_update_change_contactus_reply() {
  $contact_form = \Drupal\contact\Entity\ContactForm::load('contactus');
  $contact_form->setReply(t('Thank you for contacting us, we will reply shortly'));
  $contact_form->save();
}

/*
 The post update script is a PHP file that contains a function that will be
 executed when the update is run. The function name must start with the
 module name followed by  _post_update_  and then a unique name.
 The function should be placed in the  config_play.post_update.php  file in
 the  config_play  module.
 The function in the post update script will load the contact form with
 the machine name contactus and set the reply message to:
 Thank you for contacting us, we will reply shortly.
 The post update script will be executed when the update is run.
 Running the update
 To run the update, use the following command:
 drush updatedb

 The update will be run and the post update script will be executed.
*/
