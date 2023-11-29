<?php
namespace App\Http\Controllers\PHPMailer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PHPMailer\SMTPMailController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

const EC_HOST = "smtp.gmail.com";
const EC_USERNAME = "axay.webinopoly@gmail.com";
const EC_PASSWORD = "oqxo umue zmzv fchy";
const EC_SMPTSECURE = "ssl";
const EC_PORT = "465";
const EC_FROM_EMAIL = "axay.webinopoly@gmail.com";
const EC_FROMNAME = "";
const EC_ADDBCC = "";
const EC_CUSTOMER_REPLY_TO_EMAIL = "";

class MailController extends Controller {

    public function test_mail() {
        /* $e = $this->sendMail2('axay.webinopoly@gmail.com','','test subject','test body');
        print_r($e);
        echo 'hi'; */

        $to = 'amit@webinopoly.com';
        $subject = 'test subject';
        $mailbody = 'test body';

        $e = $this->sendMail2($to,'', $subject, $mailbody);
        echo '<pre/>';
        print_r($e);
        exit;
    }

    function sendMail2($to,$name="",$subject,$mailbody,$mailfooterbody="", $isAttachment=false, $attachmentURL="", $attachmentName="",$additional_path="") {
        $mail = new SMTPMailController();

        $mail->isSMTP();
        $mail->Host = EC_HOST;
        $mail->SMTPAuth = true;

        $mail->Username = EC_USERNAME;
        $mail->Password = EC_PASSWORD;
        $mail->From = EC_FROM_EMAIL;

        $BCCEmails = EC_ADDBCC;
        if($BCCEmails != "") {
            $BCCEmailArray = explode(',', $BCCEmails);
            for($h = 0; $h < count($BCCEmailArray); $h++){
                $mail->AddBCC($BCCEmailArray[$h]);
            }
        }

        $mail->FromName = EC_FROMNAME;
        $mail->SMTPSecure = EC_SMPTSECURE;
        $mail->Port = EC_PORT;

        $EmailArray = explode(',', $to);
        for($j = 0; $j < count($EmailArray); $j++){
            $mail->addAddress($EmailArray[$j], $name);
        }

        $ReplyTo = EC_CUSTOMER_REPLY_TO_EMAIL;
        if($ReplyTo != "") {
            $From = EC_FROMNAME;
            $mail->addReplyTo($ReplyTo, $From.' - Support');
        }

        //$mail->WordWrap = 50; // Set word wrap to 50 characters
        if($isAttachment)
            $mail->addAttachment($attachmentURL, $attachmentName); // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $mailbody;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $errorArray = array();
        if($mail->send()) {
            $errorArray["isSuccess"] = true;
        }
        else {
            $errorArray["isSuccess"] = false;
            $errorArray["msg"] = $mail->ErrorInfo;
        }
        return $errorArray;
    }
}
?>