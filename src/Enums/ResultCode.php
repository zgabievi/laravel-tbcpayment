<?php

namespace Zorb\TBCPayment\Enums;

enum ResultCode: string
{
	case Approved = 'approved';
	case DeclineGeneral = 'decline_general';
	case DeclineExpiredCard = 'decline_expired_card';
	case DeclineSuspectedFraud = 'decline_suspected_fraud';
	case DeclineRestrictedCard = 'decline_restricted_card';
	case DeclineInvalidCardNumber = 'decline_invalid_card_number';
	case DeclineNotSufficientFunds = 'decline_not_sufficient_funds';
	case DeclineCardNotEffective = 'decline_card_not_effective';
	case CheckWithAcquirer = 'check_with_acquirer';
}