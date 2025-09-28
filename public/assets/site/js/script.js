// Seleciona todos os ícones de favorito
document.querySelectorAll(".favorito-icon").forEach((icon) => {
  icon.addEventListener("click", () => {
    icon.classList.toggle("bi-heart");
    icon.classList.toggle("bi-heart-fill");
  });
});

//Cadastro de Clientes
const formCliente = document.getElementById("formCliente");
if (formCliente) {
  formCliente.addEventListener("submit", function (e) {
    e.preventDefault();

    const cliente = {
      nome: document.getElementById("inputNome").value,
      sobrenome: document.getElementById("inputSobrenome").value,
      email: document.getElementById("inputEmail").value,
      telefone: document.getElementById("inputTelefone").value,
      cidade: document.getElementById("inputCidade").value,
      estado: document.getElementById("inputEstado").value,
    };

    let clientes = JSON.parse(localStorage.getItem("clientes")) || [];
    clientes.push(cliente);
    localStorage.setItem("clientes", JSON.stringify(clientes));

    alert("Cliente cadastrado com sucesso!");
    e.target.reset();

    window.location.href = "tbclientes.html";
  });
}

//Cadastro de Vendedores
const formVendedor = document.getElementById("formVendedor");
if (formVendedor) {
  formVendedor.addEventListener("submit", function (e) {
    e.preventDefault();

    const vendedor = {
      nome: document.getElementById("inputNome").value,
      sobrenome: document.getElementById("inputSobrenome").value,
      email: document.getElementById("inputEmail").value,
      codigo: document.getElementById("inputCodigo").value,
      telefone: document.getElementById("inputTelefone").value,
      filial: document.getElementById("inputFilial").value,
    };

    let vendedores = JSON.parse(localStorage.getItem("vendedores")) || [];
    vendedores.push(vendedor);
    localStorage.setItem("vendedores", JSON.stringify(vendedores));

    alert("Vendedor cadastrado com sucesso!");
    e.target.reset();

    window.location.href = "tbvendedores.html";
  });
}

// Modal de Escolha de Cadastro
document.addEventListener("DOMContentLoaded", () => {
  const modalHTML = `
    <div class="modal fade" id="modalCadastro" tabindex="-1" aria-labelledby="modalCadastroLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCadastroLabel">Escolha seu tipo de cadastro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body text-center">
            <p class="mb-4">Selecione abaixo a opção que melhor te representa:</p>
            <div class="d-flex justify-content-around">
              <a href="cadcli.html" class="btn btn-success">Sou Cliente</a>
              <a href="cadven.html" class="btn btn-warning">Sou Vendedor</a>              
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  const div = document.createElement("div");
  div.innerHTML = modalHTML;
  document.body.appendChild(div);
});

function trocarImagem(el) {
  let imgProduto = document.getElementById("imgProduto");
  imgProduto.src = el.src;
}
