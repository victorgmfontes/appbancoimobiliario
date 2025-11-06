<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maquininha Banco Imobiliário</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #b5c4d6;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
  }

  h1 {
    margin-bottom: 10px;
  }

  .container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 40px;
  }

  .players {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .player {
    background: #fff;
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: 0.3s;
    width: 120px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
  }

  .player.selected {
    background: #4caf50;
    color: #fff;
  }

  .player h3 {
    margin: 0;
    font-size: 1rem;
  }

  .player span {
    font-weight: bold;
    color: #333;
  }

  .machine {
    background: #e0e8f0;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 10px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .display {
    width: 160px;
    height: 40px;
    background: #fff;
    border: 2px solid #ccc;
    text-align: right;
    font-size: 1.4em;
    padding: 5px;
    margin-bottom: 10px;
  }

  .keys {
    display: grid;
    grid-template-columns: repeat(3, 60px);
    grid-gap: 8px;
  }

  button {
    border: none;
    border-radius: 6px;
    font-size: 1.2em;
    cursor: pointer;
    padding: 10px;
    background: #d4d4d4;
    transition: 0.2s;
  }

  button:hover {
    background: #bbb;
  }

  .btn-m { background: #ff9800; color: white; }
  .btn-k { background: #2196f3; color: white; }
  .btn-credit { background: #4caf50; color: white; grid-column: span 3; }
  .btn-debit { background: #f44336; color: white; grid-column: span 3; }
  .btn-back { background: #4caf50; color: white; }
  .btn-reset {
    margin-top: 10px;
    background: #555;
    color: white;
    font-size: 0.9em;
  }

</style>
</head>
<body>

<h1>💳 Maquininha Banco Imobiliário</h1>

<div class="container">
  <!-- Jogadores lado esquerdo -->
  <div class="players" id="credit-side"></div>

  <!-- Maquininha -->
  <div class="machine">
    <select id="selectedPlayers" multiple size="2" style="margin-bottom:10px;">
      <option value="1">Jogador 1</option>
      <option value="2">Jogador 2</option>
      <option value="3">Jogador 3</option>
      <option value="4">Jogador 4</option>
      <option value="5">Jogador 5</option>
      <option value="6">Jogador 6</option>
    </select>

    <div class="display" id="display">0</div>

    <div class="keys">
      <button class="btn-m" onclick="applyMultiplier('M')">M</button>
      <button class="btn-back" onclick="backspace()">←</button>
      <button class="btn-k" onclick="applyMultiplier('K')">K</button>

      <button onclick="appendNumber(7)">7</button>
      <button onclick="appendNumber(8)">8</button>
      <button onclick="appendNumber(9)">9</button>

      <button onclick="appendNumber(4)">4</button>
      <button onclick="appendNumber(5)">5</button>
      <button onclick="appendNumber(6)">6</button>

      <button onclick="appendNumber(1)">1</button>
      <button onclick="appendNumber(2)">2</button>
      <button onclick="appendNumber(3)">3</button>

      <button onclick="clearDisplay()">C</button>
      <button onclick="appendNumber(0)">0</button>
      <button onclick="appendDot()">.</button>

      <button class="btn-credit" onclick="credit()">💰 Creditar</button>
      <button class="btn-debit" onclick="debit()">💸 Debitar</button>
    </div>
    <button class="btn-reset" onclick="resetGame()">🔄 Resetar saldos</button>
  </div>

  <!-- Jogadores lado direito -->
  <div class="players" id="debit-side"></div>
</div>

<script>
const players = [
  { id: 1, saldo: 15000 },
  { id: 2, saldo: 15000 },
  { id: 3, saldo: 15000 },
  { id: 4, saldo: 15000 },
  { id: 5, saldo: 15000 },
  { id: 6, saldo: 15000 }
];

function saveData() {
  localStorage.setItem("jogadoresBanco", JSON.stringify(players));
}

function loadData() {
  const data = localStorage.getItem("jogadoresBanco");
  if (data) {
    const saved = JSON.parse(data);
    for (let i = 0; i < players.length; i++) {
      players[i].saldo = saved[i]?.saldo || 15000;
    }
  }
}

loadData();
renderPlayers();

function renderPlayers() {
  document.getElementById("credit-side").innerHTML = "";
  document.getElementById("debit-side").innerHTML = "";
  players.forEach(p => {
    const el = document.createElement("div");
    el.classList.add("player");
    el.innerHTML = `<h3>Jogador ${p.id}</h3><span>R$ ${p.saldo.toLocaleString('pt-BR')}</span>`;
    if (p.id <= 3)
      document.getElementById("credit-side").appendChild(el);
    else
      document.getElementById("debit-side").appendChild(el);
  });
}

let currentInput = "";

function appendNumber(num) {
  currentInput += num;
  updateDisplay();
}

function appendDot() {
  if (!currentInput.includes(".")) {
    currentInput += ".";
    updateDisplay();
  }
}

function backspace() {
  currentInput = currentInput.slice(0, -1);
  updateDisplay();
}

function clearDisplay() {
  currentInput = "";
  updateDisplay();
}

function updateDisplay() {
  document.getElementById("display").innerText = currentInput || "0";
}

function applyMultiplier(type) {
  let value = parseFloat(currentInput);
  if (isNaN(value)) return;
  if (type === "M") value *= 1000;
  else if (type === "K") value *= 1;
  currentInput = value.toString();
  updateDisplay();
}

function getSelectedPlayers() {
  const select = document.getElementById("selectedPlayers");
  const selected = Array.from(select.selectedOptions).map(o => parseInt(o.value));
  return selected;
}

function credit() {
  const selected = getSelectedPlayers();
  let value = parseFloat(currentInput);
  if (isNaN(value)) return alert("Digite um valor válido");
  if (selected.length === 0) return alert("Selecione ao menos um jogador");

  if (selected.length === 1) {
    const p = players.find(p => p.id === selected[0]);
    p.saldo += value;
  } else if (selected.length === 2) {
    const from = players.find(p => p.id === selected[0]);
    const to = players.find(p => p.id === selected[1]);
    from.saldo -= value;
    to.saldo += value;
  }
  saveData();
  renderPlayers();
  clearDisplay();
}

function debit() {
  const selected = getSelectedPlayers();
  let value = parseFloat(currentInput);
  if (isNaN(value)) return alert("Digite um valor válido");
  if (selected.length === 0) return alert("Selecione ao menos um jogador");

  if (selected.length === 1) {
    const p = players.find(p => p.id === selected[0]);
    p.saldo -= value;
  } else if (selected.length === 2) {
    const from = players.find(p => p.id === selected[0]);
    const to = players.find(p => p.id === selected[1]);
    to.saldo -= value;
    from.saldo += value;
  }
  saveData();
  renderPlayers();
  clearDisplay();
}

function resetGame() {
  if (confirm("Tem certeza que deseja resetar todos os saldos?")) {
    players.forEach(p => p.saldo = 15000);
    saveData();
    renderPlayers();
  }
}
</script>
</body>
</html>
