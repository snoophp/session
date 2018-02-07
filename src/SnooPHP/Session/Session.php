<?php

namespace SnooPHP\Session;

/**
 * Session interface
 * 
 * Provides an interface to create, refresh and destroy a session
 * 
 * @author Sneppy
 */
abstract class Session
{
	/**
	 * @const SESSION_MAX_LENGHT session maximum length in seconds (1 year)
	 */
	const SESSION_MAX_LENGTH = 30758400;

	/**
	 * @const SESSION_DEF_LENGTH session default lnegth in secodns (1 hour)
	 */
	const SESSION_DEF_LENGTH = 3600;
	
	/* @todo */
}