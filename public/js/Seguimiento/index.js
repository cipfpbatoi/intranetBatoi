'use strict';
var options = window.seguimientoOptions || [];
options['nota'] = options['nota'] || {
	0: 'No Avaluat',
	1: '1',
	2: '2',
	3: '3',
	4: '4',
	5: '5',
	6: '6',
	7: '7',
	8: '8',
	9: '9',
	10: '10',
	11: 'MH',
	12: 'Convalida',
	13: 'Aprovada amb anterioritat'
};
options['error'] = 'Numero màxim de caracters 200';
