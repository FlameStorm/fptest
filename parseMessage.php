<?php

/**
 * Parse yandex.Money SMS with verification code
 * 
 * @param string $message
 * @return array
 * @throws Exception
 */
function parseMessage(string $message): array
{
    $result = [
        'code'     => null,
        'amount'   => null,
        'receiver' => null,
    ];
	
	
	// The first - check for yandex account number 41001XXXXX..XXX
	// Y.M. test accounts: 41003XXXXX..XXX
    preg_match_all('/(^(?:.*?\D|))(4100[\d\-]{7,})((?:[\s.,].*?|)$)/isu', $message, $matches, PREG_SET_ORDER);
    if (empty($matches)) {
        throw new \Exception('Receiver not found');
    }
    if (count($matches) > 1) {
        throw new \Exception('Multiple receivers found');
    }
	
    $result['receiver'] = preg_replace('/\D+/', '', $matches[0][2]);

	// Exclude found entity from next searches
    $message = $matches[0][1] . $matches[0][count($matches[0]) - 1];
	
	
	// Detect amount of money transfered
	// It may have 1-2 fractional digits, and may not (integer amount)
	// Also fractional part may apeears in different formats - ".", "," 
	// or even smth like "XXX р. YY к." ("...YY коп")
	$rurReg = 'р\.|р\b|руб|rub|rur|₽|&\#8381;';
	$rurFracReg = 'к\.|к\b|коп|kop';
    preg_match_all(
		"/(^(?:.*?\\s|))(
			(\\d+)((,|\\.|\\s*(?:{$rurReg})\\s*)(\\d{1,2})|)\\s*(?:{$rurReg}|{$rurFracReg})
		)((?:[\\s.,].*?|)$)/isux", $message, $matches, PREG_SET_ORDER);
    if (empty($matches)) {
        throw new \Exception('Amount not found');
    }
    if (count($matches) > 1) {
        throw new \Exception('Multiple amounts found');
    }
    $result['amount'] = floatval($matches[0][3] . '.' . $matches[0][6]);

    $message = $matches[0][1] . $matches[0][count($matches[0]) - 1];
	

	// Detect verification code (verification password)
	// For now, we sincerelly believe it must be just set of 4-6 digits
    preg_match_all('/(^|\D)(\d{4,})(\D|$)/isu', $message, $matches, PREG_SET_ORDER);
    if (empty($matches)) {
        throw new \Exception('Code not found');
    }
    if (count($matches) > 1) {
        throw new \Exception('Multiple codes found');
    }
    $result['code'] = $matches[0][2];

    return $result;
}
