SELECT 
	tests.id as id, 
	tests.name as name,
	clients.name as client,
	contracts.mkey as mkey, 
	tests.`key` as test
FROM 
	tests, 
	contracts,
	clients
WHERE 
	tests.contract_id = contracts.id
	AND contracts.client_id = clients.id 
ORDER BY 
	tests.name