<?php

namespace Zorb\TBCPayment\Enums;

enum PaymentMethod: int
{
	case WebQR = 4;
	case Card = 5;
	case ErtguliPoints = 6;
	case InternetBank = 7;
	case Installment = 8;
	case ApplePay = 9;
}