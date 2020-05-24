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
  console.log(data);
  if(data[0] == "chain.tx.new")
    app.txs.unshift(data[1]);
  if(data[0] == "chain.block.new")
    app.blocks.unshift(data[1]);
  while(app.blocks.length > MAX_LIST_SIZE)
    app.blocks.pop();
  while(app.txs.length > MAX_LIST_SIZE)
    app.txs.pop();
};
