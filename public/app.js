var app = new Vue({
  el: '#app-eth',
  data: {
    blocks: [
    ],
    txs: [
    ]
  }
});

let socket = new WebSocket("ws://" + location.host);

socket.onmessage = function(event) {
  let data = JSON.parse(event.data); 
  const MAX_LIST_SIZE = 10;
  if(data[0] == "tx")
    app.txs.push(data[1]);
  if(data[0] == "block")
    app.blocks.push(data[1]);
  while(app.blocks.length > MAX_LIST_SIZE)
    app.blocks.shift();
  while(app.txs.length > MAX_LIST_SIZE)
    app.txs.shift();
};
