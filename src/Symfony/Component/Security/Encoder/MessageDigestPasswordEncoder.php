<?php

namespace Symfony\Component\Security\Encoder;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * MessageDigestPasswordEncoder uses a message digest algorithm.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class MessageDigestPasswordEncoder extends BasePasswordEncoder
{
    protected $algorithm;
    protected $encodeHashAsBase64;

    /**
     * Constructor.
     *
     * @param string  $algorithm          The digest algorithm to use
     * @param Boolean $encodeHashAsBase64 Whether to base64 encode the password
     * @param integer $iterations         The number of iterations to use to stretch the password
     */
    public function __construct($algorithm = 'sha1', $encodeHashAsBase64 = false, $iterations = 1)
    {
        $this->algorithm = $algorithm;
        $this->encodeHashAsBase64 = $encodeHashAsBase64;
        $this->iterations = $iterations;
    }

    /**
     * {@inheritdoc}
     */
    public function encodePassword($raw, $salt)
    {
        $salted = $this->mergePasswordAndSalt($raw, $salt);
        $digest = call_user_func($this->algorithm, $salted);

        // "stretch" the encoded value
        for ($i = 1; $i < $this->iterations; $i++) {
            $digest = call_user_func($this->algorithm, $digest);
        }

        return $this->encodeHashAsBase64 ? base64_encode($digest) : $digest;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }
}