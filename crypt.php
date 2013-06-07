<?php

/* OOP Interface to php crypt() function, provides random salt generation
    USAGE:
        $crypt           = new crypt(#rounds between 04 and 31);
        $hashedpassword  = $crypt->hash('password');
        $passwordmatches = $crypt->verify('password', $hash);
        if ($passwordmatches) do things;
*/

class crypt {
    public function __construct($rounds = 15)
    {
        if (CRYPT_BLOWFISH != 1)
            throw new Exception("Crypt doesn't exist in this php install. That's kinda sad.");
        $this->rounds = $rounds;
    }

    public function hash($password)
    {
        $hash = crypt($password, $this->salt());
        if(strlen($hash) > 13) return $hash;

        return false;
    }

    private function salt()
    {
        $salt = sprintf('$2a$%02d$', $this->rounds);
        $random = $this->getRandomBytes(16);
        $salt .= $this->encodeBytes($random);
        return $salt;
    }

    public function verify($password, $dbhash)
    {
        $hash = crypt($password, $dbhash);
        return $hash === $dbhash;
    }


    private function getRandomBytes($count)
    {
        $bytes = '';
        if(function_exists('openssl_random_pseudo_bytes') &&
          (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))  // OpenSSL slow on Win
        {
            $bytes = openssl_random_pseudo_bytes($count);
        }

        if($bytes === '' && is_readable('/dev/urandom') &&
          ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE)
        {
            $bytes = fread($hRand, $count);
          fclose($hRand);
        }

        if(strlen($bytes) < $count)
        {
            $bytes = '';

            if($this->randomState === null)
            {
                $this->randomState = microtime();
                if(function_exists('getmypid'))
                    $this->randomState .= getmypid();
            }

            for($i = 0; $i < $count; $i += 16)
            {
                $this->randomState = md5(microtime() . $this->randomState);

                if (PHP_VERSION >= '5')
                    $bytes .= md5($this->randomState, true);
                else
                    $bytes .= pack('H*', md5($this->randomState));
            }

            $bytes = substr($bytes, 0, $count);
        }

        return $bytes;
    }

    private function encodeBytes($input)
    {
        // The following is code from the PHP Password Hashing Framework
        $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $output = '';
        $i = 0;
        do {
            $c1 = ord($input[$i++]);
            $output .= $itoa64[$c1 >> 2];
            $c1 = ($c1 & 0x03) << 4;
            if ($i >= 16)
            {
                $output .= $itoa64[$c1];
                break;
            }

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 4;
            $output .= $itoa64[$c1];
            $c1 = ($c2 & 0x0f) << 2;

            $c2 = ord($input[$i++]);
            $c1 |= $c2 >> 6;
            $output .= $itoa64[$c1];
            $output .= $itoa64[$c2 & 0x3f];
          } while (1);

        return $output;
    }

    private $rounds;
    private $randomState;
}

        $crypt           = new crypt();
        $hashedpassword  = $crypt->hash('password');
        $passwordmatches = $crypt->verify('password', $hashedpassword);
        echo $hashedpassword;
        if ($passwordmatches) echo "it worked!";
?>
