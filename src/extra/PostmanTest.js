pm.test("HTTP status 200", function() {
    pm.response.to.be.ok;
});

pm.test('Response is json', function() {
    pm.response.to.be.json;
});

pm.test('Response no have error', function() {
    var jsonData = pm.response.json();
    pm.expect(jsonData.error).to.be.an('undefined');
});

pm.test('Response have result' , function() {
    var jsonData = pm.response.json();
    pm.expect(jsonData.result).not.be.an('undefined');
});

pm.test('Response is jsonRpc 2.0', function() {
    var jsonData = pm.response.json();
    pm.expect(jsonData.id).not.be.an('undefined');
    pm.expect(jsonData.jsonrpc).to.be.an('string');
    pm.expect(jsonData.jsonrpc).to.equal("2.0");
});