var Buffer = require('buffer').Buffer;
var dgram = require('dgram');

var sock = dgram.createSocket("udp4");

var data = {
  "ip" : "129.59.1.10",
  "timestamp" : "Sat Oct 23 2010 21:39:35 GMT-0400 (EDT)",
  "url_key" : 123,
  "product_id" : 456
};




var buf = new Buffer(JSON.stringify(data));

console.log('buf:' + buf);

setInterval(function() {
	sock.send(buf, 0, buf.length, 8000, "0.0.0.0");
},100);

