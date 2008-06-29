<?php
class Zym_View_Helper_Mailhide
{
    public function mailhide(Zym_Recaptcha_Mailhide $mailhide)
    {
        $emailParts = $this->_splitEmail($mailhide->getEmail());
        $url = htmlentities($mailhide->url());

        foreach ($emailParts as $key => $part) {
            $emailParts[$key] = htmlentities($part);
        }

        return $emailParts[0] . '<a href="' . $url . '" onclick="window.open('
               . "'" . $url . "', '',"
               . "'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;"
               . 'title="Reveal this e-mail address">...</a>@' . $emailParts[1];
    }

    /**
     * gets the parts of the email to expose to the user.
     * eg, given johndoe@example,com return ["john", "example.com"].
     * the email is then displayed as john...@example.com
     */
    protected function _splitEmail($email)
    {
        $splitEmail = preg_split('/@/', $email);
        $length = strlen($splitEmail[0]);

        if ($length <= 4) {
            $size = 1;
        } else if ($length <= 6) {
            $size = 3;
        } else {
            $size = 4;
        }

        $splitEmail[0] = substr($splitEmail[0], 0, $size);

        return $splitEmail;
    }
}