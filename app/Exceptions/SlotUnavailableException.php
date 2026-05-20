<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown inside the booking transaction when a slot is found to be
 * occupied after acquiring the row-level lock. Caught by the API
 * controller and converted to a 422 JSON response.
 */
class SlotUnavailableException extends RuntimeException
{
}
