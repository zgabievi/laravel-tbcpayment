<?php

namespace Zorb\TBCPayment\Enums;

enum PaymentStatus: string
{
	case Created = 'Created';
	case Processing = 'Processing';
	case Succeeded = 'Succeeded';
	case Failed = 'Failed';
	case Expired = 'Expired';
	case WaitingConfirm = 'WaitingConfirm';
}