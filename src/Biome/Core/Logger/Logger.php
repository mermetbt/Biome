<?php

namespace Biome\Core\Logger;

use \Biome\Biome;

class Logger
{
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
	public static function emergency($message, array $context = array())
	{
		Biome::getService('logger')->emergency($message, $context);
	}

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public static function alert($message, array $context = array())
	{
		Biome::getService('logger')->alert($message, $context);
	}

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public static function critical($message, array $context = array())
	{
		Biome::getService('logger')->critical($message, $context);
	}

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public static function error($message, array $context = array())
	{
		Biome::getService('logger')->error($message, $context);
	}

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public static function warning($message, array $context = array())
	{
		Biome::getService('logger')->warning($message, $context);
	}

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public static function notice($message, array $context = array())
	{
		Biome::getService('logger')->notice($message, $context);
	}

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public static function info($message, array $context = array())
	{
		Biome::getService('logger')->info($message, $context);
	}

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public static function debug($message, array $context = array())
	{
		Biome::getService('logger')->debug($message, $context);
	}

}
