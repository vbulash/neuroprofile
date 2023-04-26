SELECT 
	history.id
FROM
	history,
	contracts,
	tests,
	clients
WHERE
	history.test_id = tests.id 
	AND tests.contract_id = contracts.id
	AND contracts.client_id = clients.id
	AND clients.id = 4;