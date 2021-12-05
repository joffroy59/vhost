function smartcountdown_plural(n) {
	var rest100 = n % 100;
	return n == 0 ? '_1' :
		n == 1 ? '_2' :
			n == 2 ? '_3' :
				rest100 >= 3 && rest100 <= 10 ? '_4' :
					rest100 >= 11 ? '' : // no suffix for plural > 10
						'_5';
}