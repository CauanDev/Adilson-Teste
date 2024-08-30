<template>
    <!-- Indicador de carregamento exibido enquanto dados estão sendo carregados -->
    <LoadingCircle v-if="loading" />
    <div>
        <!-- Aviso de erro quando não há resultados na consulta -->
        <WrongWarning title="Consulta Retornou Zero" @close="close" v-if="warning" />

        <!-- Componente para exibir o título da página -->
        <TitleView title="DashBoard" />

        <!-- Menu de seleção para alterar a categoria exibida -->
        <SelectMenu @categoryChanged="handleCategoryChange" />

        <!-- Exibe o conteúdo específico da categoria selecionada -->
        <div v-if="categoria === 'funcionarios'" class="py-4 flex flex-col justify-left">
            <!-- Filtro para funcionários -->
            <FuncionarioFilter @applyFilter="applyFilter" />

            <!-- Layout para exibir gráficos e tabelas relacionados aos funcionários -->
            <div class="flex space-x-4 gap-[100px] mobile">
                <div class="lg:w-[30%]">
                    <!-- Gráficos de média salarial e idade dos funcionários -->
                    <MediaSalarial :body="body" />
                    <div class="flex mobile justify-center items-center">
                        <QuantidadeServicos :body="quantidade" />
                        <MediaIdade :body="body" class="w-[50%]" />
                    </div>
                </div>
                <div class="lg:w-[45%]">
                    <!-- Tabela de dashboard de funcionários -->
                    <FuncionarioDashboardTable :body="body" />
                </div>
            </div>
        </div>

        <div v-if="categoria === 'clientes'">
            <!-- Filtro para clientes -->
            <ClientesFilter @applyFilter="applyFilter" />
            <!-- Layout para exibir gráficos e tabelas relacionados aos clientes -->
            <div class="flex space-x-4 gap-[100px] mobile">
                <div class="lg:w-[30%]">
                    <div class="flex mobile justify-center items-center flex-col ml-2">
                        <div class="w-full flex">
                            <QuantidadeServicos :body="quantidade" />
                            <MediaIdade :body="body" class="w-[55%]" />                            
                        </div>

                        <div class="w-full"> 
                            <ContagemTipoCliente :body="body" class="w-[50%]"/>
                        </div>
                    </div>
                </div>
                <div class="w-[45%]">
                    <!-- Tabela de dashboard de clientes -->
                    <ClienteDashboardTable :body="body" />
                </div>
            </div>
        </div>

        <div v-if="categoria === 'produtos'">
            <!-- Filtro para produtos -->
            <ProdutosFilter @applyFilter="applyFilter" />
            <!-- Layout para exibir gráficos e tabelas relacionados aos produtos -->
            <div class="flex gap-[40px] mobile w-full h-ful">
                <div class=" lg:pl-2  w-full">
                    <!-- Gráficos de contagem de marcas e média de produtos -->
                    <ContagemMarca :data="body" class="w-[80%]"/>
                    <ProdutoMedia :produtos="body" class="w-[80%]"/>
                </div>
                <div>
                    <!-- Tabela de dashboard de produtos -->
                    <ProdutoDashboardTable :body="body" />
                </div>
            </div>
        </div>

        <div v-if="categoria === 'pedidos'">
            <!-- Filtro para pedidos -->
            <PedidosFilter @applyFilter="applyFilter" />
            <!-- Layout para exibir gráficos e tabelas relacionados aos pedidos -->
            <div class="flex space-x-4 gap-[80px] mobile">
                <div class="md:w-[30%] ml-2">
                    <!-- Gráfico de contagem de vendas -->
                    <ContagemVendas :salesData="body" />
                </div>
                <div class="w-[45%]">
                    <!-- Tabela de dashboard de pedidos -->
                    <PedidoDashboardTable :body="body" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ContagemTipoCliente from "../src/components/Graficos/ContagemTipoCliente.vue";
import MediaSalarial from "../src/components/Graficos/Funcionarios/MediaSalarial.vue";
import MediaIdade from "../src/components/Graficos/Funcionarios/MediaIdade.vue";
import QuantidadeServicos from "../src/components/Graficos/Funcionarios/QuantidadeServicos.vue";
import WrongWarning from "../src/components/Warnings/WrongWarning.vue"
import TitleView from "../src/components/Title/TitleView.vue";
import SelectMenu from "../src/components/Inputs/SelectMenu.vue";
import FuncionarioFilter from "../src/components/DashBoard/FuncionarioFilter.vue";
import ClientesFilter from "../src/components/DashBoard/ClientesFilter.vue";
import ProdutosFilter from "../src/components/DashBoard/ProdutosFilter.vue"
import PedidosFilter from "../src/components/DashBoard/PedidosFilter.vue";
import FuncionarioDashboardTable from "../src/components/Tables/FuncionarioTable/FuncionarioDashboardTable.vue";
import ClienteDashboardTable from "../src/components/Tables/ClienteTable/ClienteDashboardTable.vue";
import ProdutoDashboardTable from "../src/components/Tables/ProdutosTable/ProdutoDashboardTable.vue";
import PedidoDashboardTable from "../src/components/Tables/PedidoTable/PedidoDashboardTable.vue";
import ProdutoMedia from "../src/components/Graficos/Produtos/ProdutoMedia.vue";
import ContagemMarca from "../src/components/Graficos/Produtos/ContagemMarca.vue";
import ContagemVendas from "../src/components/Graficos/ContagemVendas.vue";
import LoadingCircle from "../src/components/Loading/LoadingCircle.vue";
import http from "../src/services/http";

export default {
    name: 'DashBoard',
    components: {
        ContagemTipoCliente,LoadingCircle, ContagemVendas, ProdutoMedia, PedidoDashboardTable, PedidosFilter, ContagemMarca, ProdutosFilter,
        ProdutoDashboardTable, ClienteDashboardTable, ClientesFilter, QuantidadeServicos, MediaIdade, MediaSalarial,
        TitleView, SelectMenu, FuncionarioFilter, FuncionarioDashboardTable, WrongWarning
    },
    data() {
        return {
            categoria: null, // Categoria selecionada no menu
            body: [], // Dados a serem exibidos nas tabelas e gráficos
            warning: false, // Controle para exibir o aviso de erro
            quantidade: [], // Dados de quantidade usados em gráficos
            loading: false, // Controle de estado de carregamento
        };
    },
    methods: {
        // Fecha o aviso de erro
        close() {
            this.warning = false;
        },
        resetState() {
            this.body = [];
            this.quantidade = [];
            this.warning = false;
            this.loading = false;
        },
        // Manipula a mudança de categoria e carrega dados correspondentes
        handleCategoryChange(selectedCategory) {
            this.categoria = selectedCategory;
            this.resetState();
            if (this.categoria === 'funcionarios') this.getFuncionarios();
            if (this.categoria === 'clientes') this.getClientes();
            if (this.categoria === 'produtos') this.getProdutos();
            if (this.categoria === 'pedidos') this.getPedidos();
        },

        // Obtém a lista de funcionários e a quantidade por sexo
        async getFuncionarios() {
            try {
                const data = await http.get('/funcionarios');
                const quantidade = await http.get('/funcionarios/por-sexo');
                this.quantidade = quantidade.data;
                this.setFuncionarios(data.data.funcionarios)
 
            } catch (error) {
                console.log(error);
            }
            this.loading = false; // Finaliza o carregamento
        },
        setFuncionarios(array)
        {
            this.resetState()
            this.body = array.map((item) => {
                    return {
                        ...item,
                        "idade": this.calcularIdade(item.data_nasc)
                    }
                })
        },
        calcularIdade(dataNasc) {
            const hoje = new Date();
            const nascimento = new Date(dataNasc);

            let idade = hoje.getFullYear() - nascimento.getFullYear();
            const mes = hoje.getMonth() - nascimento.getMonth();

            // Ajusta a idade se o mês de nascimento ainda não foi alcançado ou se a data de nascimento ainda não passou este ano
            if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                idade--;
            }

            return idade;
        },

        // Obtém a lista de pedidos e define os dados para a tabela
        async getPedidos() {
            try {
                const pedidos = await http.get('/pedidos');
                this.setPedidos(pedidos.data.pedidos);
            } catch (error) {
                console.log(error);
            }
        },

        // Define os pedidos com detalhes adicionais (cliente e funcionário)
        async setPedidos(array) {
            this.body = await Promise.all(array.map(async pedido => {
                const produtos = (pedido.produtos);
                const [clienteResponse, funcionarioResponse] = await Promise.all([
                    http.get(`/cliente/${pedido.cliente_id}`),
                    http.get(`/funcionario/${pedido.funcionario_id}`)
                ]);
                return {
                    id: pedido.id,
                    preco: pedido.total,
                    status: pedido.status,
                    produtos: pedido.length,
                    tudo: pedido,
                    created_at: pedido.created_at,
                    cliente_nome: clienteResponse.data.cliente[0].nome,
                    funcionario_nome: funcionarioResponse.data.funcionario[0].nome
                };
            }));
            this.loading = false; // Finaliza o carregamento
        },

        // Obtém a lista de clientes e a quantidade por sexo
        async getClientes() {
            try {
                const data = await http.get('/clientes');
                const quantidade = await http.get('/clientes/por-sexo');
                this.quantidade = quantidade.data;
                this.body = data.data.clientes;
            } catch (error) {
                console.log(error);
            }
            this.loading = false; // Finaliza o carregamento
        },

        // Aplica o filtro baseado no tipo de categoria
        async applyFilter(filter, type) {
            this.loading = true; // Inicia o carregamento
            try {
                let response;
                if (type == 'funcionarios') {
                    response = await http.post('/filter-funcionario', filter);
                    this.setFuncionarios(response.data.funcionarios)
                    if (response.data.funcionarios.length == 0) this.warning = true;
                    else this.body = response.data.funcionarios;
                } else if (type == 'clientes') {
                    response = await http.post('/filter-cliente', filter);
                    if (response.data.clientes.length == 0) this.warning = true;
                    else this.body = response.data.clientes;
                } else if (type == 'produtos') {
                    response = await http.post('/filter-produto', filter);
                    if (response.data.produtos.length == 0) this.warning = true;
                    else this.setProdutos(response.data.produtos);
                } else if (type == 'pedidos') {
                    response = await http.post('/filter-pedidos', filter);
                    if (response.data.pedidos.length == 0) this.warning = true;
                    else this.setPedidos(response.data.pedidos);
                }
            } catch (error) {
                console.log(error);
            }
            this.loading = false; // Finaliza o carregamento
        },
        // Obtém a lista de produtos e define os dados para a tabela
        async getProdutos() {
            try {
                const data = await http.get('/produtos');
                this.setProdutos(data.data.produtos);
            } catch (error) {
                console.log(error);
            }
        },

        // Define os produtos com detalhes adicionais (fornecedor e tempo médio de compra)
        async setProdutos(array) {
            const promises = array.map(async produto => {
                try {
                    // const fornecedorResponse = await http.get('/fornecedor/' + produto.fornecedor_id);
                    const mediaTempoCompra = await http.post('/media-pedidos', { "name": produto.name });
                    return {
                        ...produto,
                        tempoMedioDias: mediaTempoCompra.data.tempo_medio_dias,
                        "fornecedorNome":mediaTempoCompra.data.fornecedor_nome
                    };
                } catch (error) {
                    // console.error(`Erro ao buscar fornecedor para o produto ${produto.id}:`, error);
                    // return produto;
                }
            });
            this.body = await Promise.all(promises);
            this.loading = false;
        }
    }
};
</script>
