<?php
/**
 * Original ZF3 validator, extended for ID lenght test.
 * @see       https://github.com/laminas/laminas-session for the canonical source repository
 * @copyright Copyright (c) 2005-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/laminas/laminas-session/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Session\Validator;

/**
 * session_id validator
 */
class Id2 implements ValidatorInterface
{
    /**
     * Session identifier.
     *
     * @var string
     */
    protected $id;

    /**
     * Constructor
     *
     * Allows passing the current session_id; if none provided, uses the PHP
     * session_id() function to retrieve it.
     *
     * @param null|string $id
     */
    public function __construct($id = null)
    {
        if (empty($id)) {
            $id = session_id();
        }

        $this->id = $id;
    }

    /**
     * Is the current session identifier valid?
     *
     * Tests that the identifier does not contain invalid characters.
     *
     * @return bool
     */
    public function isValid()
    {
        $id = $this->id;
        $saveHandler = ini_get('session.save_handler');
        if ($saveHandler == 'cluster') { // Zend Server SC, validate only after last dash
            $dashPos = strrpos($id, '-');
            if ($dashPos) {
                $id = substr($id, $dashPos + 1);
            }
        }

        // Get the session id bits per character INI setting, using 5 if unavailable
        $bitsPerCharacter = PHP_VERSION_ID >= 70100
            ? 'session.sid_bits_per_character'
            : 'session.hash_bits_per_character';
        $hashBitsPerChar = ini_get($bitsPerCharacter) ?: 5;

        $sidLength = ini_get('session.sid_length');

        switch ($hashBitsPerChar) {
            case 4:
                $pattern = "#^[0-9a-f]{{$sidLength}}$#";
                break;
            case 6:
                $pattern = "#^[0-9a-zA-Z-,]{{$sidLength}}$#";
                break;
            case 5:
                // intentionally fall-through
            default:
                $pattern = "#^[0-9a-v]{{$sidLength}}$#";
                break;
        }

        return preg_match($pattern, $id) === 1;
    }

    /**
     * Retrieve token for validating call (session_id)
     *
     * @return string
     */
    public function getData()
    {
        return $this->id;
    }

    /**
     * Return validator name
     *
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }
}
