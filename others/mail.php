<?php
/* This code is based on https://github.com/jemjabella/PHP-Mail-Form (GNU General Public License) and was customized by http://ganz-sicher.net */

/* OPTIONS - PLEASE CONFIGURE THESE BEFORE USE! */
$yourEmail = "elisabeth.grzeca@koneca.de"; // the email address you wish to receive these mails through
$yourWebsite = "Die-Dinkelmaus.de"; // the name of your website
$thanksPage = ''; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page
$maxPoints = 7; // max points a person can hit before it refuses to submit - recommend 4
$requiredFields = "name,nachricht"; // names of the fields you'd like to be required as a minimum, separate each field with a comma

/* DO NOT EDIT BELOW HERE */
$error_msg = array();
$result = null;
$requiredFields = explode(",", $requiredFields);

/* cleanup data before sending */
function clean($data) {
    $data = trim(stripslashes(strip_tags($data)));
    return $data;
}

/* simple function to filter out some bots  */
function isBot() {
    $bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz");

    foreach ($bots as $bot) {
        if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
            return true;
    }

    if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
        return true;

    return false;
}

/* read out field values (with HTML codes 4 special chars) */
function get_data($var) {
    if (isset($_POST[$var]))
        echo htmlspecialchars($_POST[$var]);
}

/* process form after submit */
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isBot() !== false)
        $error_msg[] = "No bots please! UA reported as: ".$_SERVER['HTTP_USER_AGENT'];

    // lets check a few things - not enough to trigger an error on their own, but worth assigning a spam score..
    // score quickly adds up therefore allowing genuine users with 'accidental' score through but cutting out real spam :)
    $points = (int)0;

    $badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur", "content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript");

    foreach ($badwords as $word)
        if (
            strpos(strtolower($_POST['nachricht']), $word) !== false ||
            strpos(strtolower($_POST['name']), $word) !== false
        )
            $points += 2;

    if (strpos($_POST['nachricht'], "http://") !== false || strpos($_POST['nachricht'], "www.") !== false)
        $points += 2;
    if (isset($_POST['nojs']))
        $points += 1;
    if (preg_match("/(<.*>)/i", $_POST['nachricht']))
        $points += 2;
    if (strlen($_POST['name']) < 3)
        $points += 1;
    if (strlen($_POST['nachricht']) < 15 || strlen($_POST['nachricht'] > 1500))
        $points += 2;
    if (preg_match("/[bcdfghjklmnpqrstvwxyz]{7,}/i", $_POST['nachricht']))
        $points += 1;
    // end score assignments

    foreach($requiredFields as $field) {
        trim($_POST[$field]);

        if (!isset($_POST[$field]) || empty($_POST[$field]) && array_pop($error_msg) != "Please fill in all the required fields and submit again.\r\n") {
            $error_msg[] = "Bitte alle notwendigen Felder ausfüllen und erneut abschicken.";
            break;
        }
    }

    if (!empty($_POST['name']) && !preg_match("/^[a-zA-Z-'\s]*$/", stripslashes($_POST['name'])))
        $error_msg[] = "Der Name darf keine Sonderzeichen enthalten!\r\n";
    if (!empty($_POST['email']) && !preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', strtolower($_POST['email'])))
        $error_msg[] = "Ung&uuml;ltige Mailadresse. Bitte korrigieren.\r\n";
    if (!empty($_POST['website']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['website']))
        $error_msg[] = "Ung&uuml;ltige Website-Url. Bitte korrigieren.\r\n";

    if ($error_msg == NULL && $points <= $maxPoints) {
        $subject = "Neue Mail von Die-Dinkelmaus.de";

        $message = "Folgende Nachricht wurde über die Website verschickt: \n\n";
        foreach ($_POST as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $subval) {
                    if (empty(clean(trim($subval)))) { continue; }   // skip empty fields
                    $message .= ucwords($key) . ": \r\n" . clean(trim($subval)) . "\r\n\r\n";
                }
            } else {
                if (empty(clean(trim($val)))) { continue; }   // skip empty fields
                $message .= ucwords($key) . ": \r\n" . clean($val) . "\r\n\r\n";
            }
        }
        $message .= "\r\n";

        $headers   = "From: $yourWebsite <$yourEmail>\r\n";
        $headers  .= "Reply-To: {$_POST['email']}\r\n";

        // try to send mail
        if (mail($yourEmail, $subject, $message, $headers)) {

            // send copy mail for maintenance
            $message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
            $message .= 'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
            $message .= 'Points: '.$points;
            $myEmail = "sjmk89@gmail.com";
            $headers   = "From: $yourWebsite <$myEmail>\r\n";
            $headers  .= "Reply-To: {$_POST['email']}\r\n";
            mail($myEmail, $subject, $message, $headers);

            if (!empty($thanksPage)) {
                header("Location: $thanksPage");
                exit;
            } else {
                $result = 'E-Mail erfolgreich verschickt. Danke f&uuml;r die Nachricht!';
                $disable = true;
            }
        } else {
            $error_msg[] = 'Die Mail konnte nicht verschickt werden. (Spam-Sperre aufgrund zu hoher Zugriffszahl) ['.$points.']';
        }
    } else {
        if (empty($error_msg))
            $error_msg[] = 'Die Nachricht wurde nicht verschickt (Spamverdacht). ['.$points.']';
    }

    /* result output */
    if (!empty($error_msg)) {
        echo '<p class="alert alert-danger">FEHLER: '. implode("<br />", $error_msg) . "</p>";
    }
    if ($result != NULL) {
        echo '<p class="alert alert-success">'. $result . "</p>";
    }
}
?>

<form id="mailform" action="" method="post">
    <noscript>
        <p><input type="hidden" name="nojs" id="nojs" /></p>
    </noscript>

    <div class="input-group">
        <input class="form-control" placeholder="Name (erforderlich)" title="Name (erforderlich)" type="text" name="name" id="name" value="<?php get_data("name"); ?>" />
        <input class="form-control" placeholder="E-Mail (optional)" title="E-Mail (optional)" type="text" name="email" id="email" value="<?php get_data("email"); ?>"  />
        <input class="form-control" placeholder="Adresse der eigenen Website (optional)" title="Adresse der eigenen Website (optional)" type="text" name="website" id="website" value="<?php get_data("website"); ?>" />

        <textarea placeholder="Nachricht (erforderlich)"  title="Ihre Nachricht (erforderlich)" class="form-control" name="nachricht" id="nachricht" rows="5" cols="20"><?php get_data("nachricht"); ?></textarea><br />

        <?php if (!$disable) {
            echo '<button type="submit" name="submit" id="submit" class="btn btn-default btn-lg">
            <span class="glyphicon glyphicon-send"></span> Abschicken!
            </button>';
        }
        ?>
    </div>

</form>