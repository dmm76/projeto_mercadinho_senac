// =====================
// 1) Ícone de favorito
// =====================
document.querySelectorAll(".favorito-icon").forEach((icon) => {
  icon.addEventListener("click", () => {
    icon.classList.toggle("bi-heart");
    icon.classList.toggle("bi-heart-fill");
  });
});

// ===========================
// 2) Cadastro de Clientes (LS)
// ===========================
const formCliente = document.getElementById("formCliente");
if (formCliente) {
  formCliente.addEventListener("submit", function (e) {
    e.preventDefault();

    const cliente = {
      nome: document.getElementById("inputNome")?.value || "",
      sobrenome: document.getElementById("inputSobrenome")?.value || "",
      email: document.getElementById("inputEmail")?.value || "",
      telefone: document.getElementById("inputTelefone")?.value || "",
      cidade: document.getElementById("inputCidade")?.value || "",
      estado: document.getElementById("inputEstado")?.value || "",
      cpf: document.getElementById("cliente-cpf")?.value || ""
    };

    let clientes = JSON.parse(localStorage.getItem("clientes")) || [];
    clientes.push(cliente);
    localStorage.setItem("clientes", JSON.stringify(clientes));

    alert("Cliente cadastrado com sucesso!");
    e.target.reset();

    window.location.href = "tbclientes.html";
  });
}

// ============================
// 3) Cadastro de Vendedores (LS)
// ============================
const formVendedor = document.getElementById("formVendedor");
if (formVendedor) {
  formVendedor.addEventListener("submit", function (e) {
    e.preventDefault();

    const vendedor = {
      nome: document.getElementById("inputNome")?.value || "",
      sobrenome: document.getElementById("inputSobrenome")?.value || "",
      email: document.getElementById("inputEmail")?.value || "",
      codigo: document.getElementById("inputCodigo")?.value || "",
      telefone: document.getElementById("inputTelefone")?.value || "",
      filial: document.getElementById("inputFilial")?.value || "",
    };

    let vendedores = JSON.parse(localStorage.getItem("vendedores")) || [];
    vendedores.push(vendedor);
    localStorage.setItem("vendedores", JSON.stringify(vendedores));

    alert("Vendedor cadastrado com sucesso!");
    e.target.reset();

    window.location.href = "tbvendedores.html";
  });
}

// =====================================
// 4) Modal de Escolha de Cadastro (DOM)
// =====================================
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

// ======================
// 5) Troca de Imagem
// ======================
function trocarImagem(el) {
  let imgProduto = document.getElementById("imgProduto");
  if (imgProduto && el?.src) imgProduto.src = el.src;
}
window.trocarImagem = trocarImagem; // expõe no escopo global caso usado inline

// ======================
// 6) Máscaras leves
// ======================

// CPF: suporta #conta-cpf e #cliente-cpf
document.addEventListener("input", function (e) {
  const el = e.target;

  // Máscara CPF
  if (el.id === "conta-cpf" || el.id === "cliente-cpf") {
    let v = (el.value || "").replace(/\D/g, "").slice(0, 11);
    if (v.length > 9) v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/, "$1.$2.$3-$4");
    else if (v.length > 6) v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, "$1.$2.$3");
    else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3}).*/, "$1.$2");
    el.value = v;
  }

  // Telefone: #conta-telefone e #inputTelefone
  if (el.id === "conta-telefone" || el.id === "inputTelefone") {
    let v = (el.value || "").replace(/\D/g, "").slice(0, 11); // até 11 dígitos
    if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
    else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5}).*/, "($1) $2");
    el.value = v;
  }

  // CEP: #endereco-cep
  if (el.id === "endereco-cep") {
    let v = (el.value || "").replace(/\D/g, "").slice(0, 8);
    if (v.length > 5) v = v.slice(0, 5) + "-" + v.slice(5);
    el.value = v;

    // auto-dispara busca quando completa 8 dígitos
    if (v.replace(/\D/g, "").length === 8) {
      buscarCEPAuto();
    }
  }
});

// =======================================
// 7) Autocomplete de Endereço via CEP
//    (BrasilAPI → ViaCEP fallback)
// =======================================
const cepInput = document.getElementById("endereco-cep");
const statusEl = document.getElementById("cep-status");
const logradouroEl = document.getElementById("endereco-logradouro");
const bairroEl = document.getElementById("endereco-bairro");
const cidadeEl = document.getElementById("endereco-cidade");
const ufEl = document.getElementById("endereco-uf");
const submitBtn = document.querySelector('button[type="submit"]');

function setCepStatus(msg, type = "muted") {
  if (!statusEl) return;
  statusEl.textContent = msg || "";
  statusEl.classList.remove("text-muted", "text-danger", "text-success");
  statusEl.classList.add(type === "error" ? "text-danger" : type === "success" ? "text-success" : "text-muted");
}

function toggleEnderecoLoading(on) {
  const toggle = (el) => el && el.toggleAttribute("disabled", on);
  [logradouroEl, bairroEl, cidadeEl, ufEl, cepInput, submitBtn].forEach(toggle);
}

async function fetchComTimeout(url, ms = 6000) {
  const ctrl = new AbortController();
  const id = setTimeout(() => ctrl.abort(), ms);
  try {
    const res = await fetch(url, { signal: ctrl.signal });
    return res;
  } finally {
    clearTimeout(id);
  }
}

async function buscaCEPComFallback(cep) {
  // 1) BrasilAPI
  try {
    const r = await fetchComTimeout(`https://brasilapi.com.br/api/cep/v1/${cep}`);
    if (r.ok) {
      const j = await r.json();
      if (j?.cep) {
        return {
          cep: (j.cep || "").replace(/\D/g, ""),
          logradouro: j.street || "",
          bairro: j.neighborhood || "",
          cidade: j.city || "",
          uf: j.state || "",
          fonte: "BrasilAPI",
        };
      }
    }
  } catch (e) {
    // segue pro fallback
  }

  // 2) ViaCEP
  const r2 = await fetchComTimeout(`https://viacep.com.br/ws/${cep}/json/`);
  if (!r2.ok) throw new Error("Erro ao consultar ViaCEP.");
  const j2 = await r2.json();
  if (j2.erro) throw new Error("CEP não encontrado.");
  return {
    cep: (j2.cep || "").replace(/\D/g, ""),
    logradouro: j2.logradouro || "",
    bairro: j2.bairro || "",
    cidade: j2.localidade || "",
    uf: j2.uf || "",
    fonte: "ViaCEP",
  };
}

function preencherEndereco({ logradouro, bairro, cidade, uf }) {
  if (logradouroEl) logradouroEl.value = logradouro || "";
  if (bairroEl) bairroEl.value = bairro || "";
  if (cidadeEl) cidadeEl.value = cidade || "";
  if (ufEl && uf) {
    const options = Array.from(ufEl.options).map((o) => o.value);
    if (options.includes(uf)) ufEl.value = uf;
  }
}

async function buscarCEPAuto() {
  if (!cepInput) return;
  const raw = (cepInput.value || "").replace(/\D/g, "");
  if (raw.length !== 8) return;

  setCepStatus("Buscando CEP…");
  toggleEnderecoLoading(true);
  try {
    const data = await buscaCEPComFallback(raw);
    preencherEndereco(data);
    setCepStatus(`Endereço encontrado (${data.fonte}).`, "success");
  } catch (e) {
    setCepStatus(e?.message || "Não foi possível buscar o CEP.", "error");
  } finally {
    toggleEnderecoLoading(false);
  }
}

// também busca ao sair do campo
if (cepInput) {
  cepInput.addEventListener("blur", buscarCEPAuto);
}
