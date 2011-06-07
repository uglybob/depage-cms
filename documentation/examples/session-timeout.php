<?php
/*
 * Load the library...
 */
require_once('../../htmlform.php');

/*
 * Create the example form 'timeoutForm', set the session expiry to 3 seconds.
 * (this is mainly to demonstrate the feature, 3 seconds will probably irritate
 * users)
 */
$form = new depage\htmlform\htmlform('timeoutForm', array('ttl' => 3));

/*
 * Add input elements
 */
$form->addText('username', array('label' => 'User name'));
$form->addEmail('email', array('label' => 'Email address'));

/*
 * The process method is essential to the functionality of the form. It serves
 * various purposes:
 *  - it validates submitted data if there is any
 *  - it redirects to the success page if all the data is valid
 *  - it stores the data in the session and redirects to the form to circumvent
 *    the form resubmission problem
 */
$form->process();

/*
 * Finally, if the form is valid, dump the data (for demonstration). If it's
 * not valid (or if it hasn't been submitted yet) display the form.
 */
if ($form->validate()) {
    /*
     * Success, do something useful with the data and clear the session.
     * The getValues method returns an array of the form element values.
     */
    echo('<pre>');
    var_dump($form->getValues());
    echo('</pre>');

    $form->clearSession();
} else {
    echo ('<link type="text/css" rel="stylesheet" href="test.css">');
    /*
     * Display the form.
     */
    echo ($form);
}
