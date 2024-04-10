<?php
ob_start();

// Function to get email template
function getEmailTemplate($template, $client_name, $client_equipment, $due_date = null) {
    $templates = array(
        'ready_to_pickup' => "Hi $client_name," . PHP_EOL . PHP_EOL . "The following equipment is ready for you to pick up from the Student Media Center (SMC):" . PHP_EOL . PHP_EOL . "$client_equipment" . PHP_EOL . PHP_EOL . "Pickup Instructions:" . PHP_EOL . "- Pick up equipment at the SMC during open hours Monday-Thursday; please check SMC service hours on the SMC website before coming." . PHP_EOL . " - Bring your photo ID." . PHP_EOL . " - Follow campus safety and entry procedures." . PHP_EOL . PHP_EOL . "For more information, refer to the Cameras & Equipment page on our website." . PHP_EOL . PHP_EOL . "SMC Location: The SMC is on the upper floor of the Library Building (Room 3202). You must enter the building from the library's main floor and take the stairs inside the library to the upper floor." . PHP_EOL . PHP_EOL . "Note: There are two entrances to the Library Building: one on the south side of the building at the courtyard with the flagpoles, and the other one connected to the covered walkway along the east side of the building." . PHP_EOL . PHP_EOL . "Let us know if you have any questions or concerns." . PHP_EOL . PHP_EOL . "Best," . PHP_EOL . "SMC team",
        'returned_confirmation' => "Hi $client_name," . PHP_EOL . PHP_EOL . "Thank you for returning the SMC equipment. We have processed your return and you're all set. Please feel free to stop by again if you need to check out anything else or use the SMC's services." . PHP_EOL . PHP_EOL . "If you have a minute, we would also appreciate it if you filled out the feedback form. Your feedback is important to us!" . PHP_EOL . PHP_EOL . "Best," . PHP_EOL . "SMC",
        'due_date_confirmation' => "Hello $client_name," . PHP_EOL . PHP_EOL . "This is a reminder that the equipment kits you checked out are due on:" . PHP_EOL . PHP_EOL . "$due_date" . PHP_EOL . PHP_EOL . "You will need to come in person to return or renew the equipment. Please bring all the items with you, so SMC staff can check the equipment's condition. Please check SMC service hours on the SMC website before coming." . PHP_EOL . PHP_EOL . "Attached is the checklist of your equipment." . PHP_EOL . PHP_EOL . "Please note the SMC policy does not allow equipment to be checked out or renewed for longer than one week at a time if items are not returned or checked in with SMC staff on time and by the due date." . PHP_EOL . PHP_EOL . "Location: The SMC is on the upper floor of the Library Building (Room 3202). You must enter the building from the library's main floor and take the stairs inside the library to the upper floor." . PHP_EOL . PHP_EOL . "Please let us know if you need more information or have any questions." . PHP_EOL . PHP_EOL . "Best," . PHP_EOL . "SMC"
        // Add more templates as needed
    );
    
    return isset($templates[$template]) ? $templates[$template] : '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if form data is present
    if (isset($_POST['client_name'], $_POST['client_email'], $_POST['client_equipment'], $_POST['email_template'])) {
        // Retrieve form data
        $client_names = $_POST['client_name'];
        $client_emails = $_POST['client_email'];
        $client_equipments = $_POST['client_equipment'];
        $email_templates = $_POST['email_template'];
        
        // Loop through each form submission
        for ($i = 0; $i < count($client_names); $i++) {
            // Assign values from current form submission
            $client_name = $client_names[$i];
            $client_email = $client_emails[$i];
            $client_equipment = $client_equipments[$i];
            $email_template = $email_templates[$i];
            
            // Set due_date_confirmation and due_date_input if available
            $due_date_confirmation = '';
            $due_date_input = '';
            if (isset($_POST['due_date_confirmation'][$i])) {
                $due_date_confirmation = $_POST['due_date_confirmation'][$i];
                // Check if the due_date_input is set before accessing it
                $due_date_input = isset($_POST['due_date_input'][$i]) ? $_POST['due_date_input'][$i] : '';
            }

            $to = $client_email;
            $subject = "SMC's Equipment: " . ucfirst(str_replace('_', ' ', $email_template)); // Subject modified
            
            // Set due_date based on due_date_confirmation
            $due_date = null;
            if ($email_template === 'due_date_confirmation' && $due_date_confirmation === 'Yes') {
                $due_date = date("l, F j, Y", strtotime($due_date_input)); // Formatting due date
            }
            
            // Get email body based on selected template
            $body = getEmailTemplate($email_template, $client_name, $client_equipment, $due_date);

            $headers = array(
                'From' =>'nscmediacenter@seattlecolleges.edu', // Replace with your email
            );

            // Check if all required fields are present and not empty
            if (!empty($client_name) && !empty($client_email) && !empty($client_equipment) && !empty($email_template)) {
                // Send email
                $success = mail($to, $subject, $body, implode("\r\n", $headers));
                if (!$success) {
                    echo "Failed to send email to $to. Please try again later.";
                }
            }
        }
        
        // Redirect after processing all form submissions
        header('Location: thx.html');
        exit;
    }
}
?>
