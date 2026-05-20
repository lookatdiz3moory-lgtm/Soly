<?php

namespace App\Policies;

class DoctorPolicy extends BasePolicy
{
    // Inherits defaults — only admin+ can manage doctor records.
    // Secretaries and doctors can view but not modify.
}
