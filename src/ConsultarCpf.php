<?php
namespace AltanirBarbosa\ConsultaCPF;
/**
 * Classe buscaConsultaCPF
 * Classe Abstrata para definição de similaridades entre as demais classes
 * @author Rodrigo AltanirBarbosa <rjarouche@gmail.com>
 * @package ConsultaCPF
 * @version 0.1
 */

class ConsultaCPF
{
    /** 
     * Constante que indica qual o cpf da requisição
     * @var string CPF_SITE 
     */
    const CPF_SITE = 'https://api.cpfcnpj.com.br';
    
    /** 
     * 
     *Constante utilizada para dizer o sub-cpf da requisição
     *p.ex para a requisição https://ws.hubdodesenvolvedor.com.br/v2/cpf/ o valor da variável será '/querty/'
     *Deve ser especificada nas classes filhas que formalmente farão a implementação do método de busca 
     * @var string CPF_METHOD 
    */
    const CPF_METHOD = '';

    const CPF_CODCONSULTA = '2';
    /** 
     * Armazena cpf pré-formatado para busca p.ex 09190099
     * @var string $cpf 
     */
    const CPF_TOKEN = '51c5070892909dd4340d1dc1814b44d9';
    /** 
     * Armazena cpf pré-formatado para busca p.ex 09190099
     * @var string $cpf 
     */
    protected $cpf;
     /** 
      *Armazena o retorno bruto da requisição
      *@var string $results_string 
     */
     protected $results_string;
    /** 
      *Parâmetros extra para requisição
      *@var string $outros_parametros 
     */
    protected $outros_parametros;

    public function __construct()
    {
    }

     /**
     * Método validaCEP
     * Método para a formatação e validação do cpf a ser pesquisado
     * @param string $cpf
     * @return void;
      * @throws Exception
     */
     protected function validaCPF($cpf)
     {
        $formated_cep = preg_replace("/[^0-9]/", "", $cpf);
        if (!preg_match('/^[0-9]{8}?$/', $formated_cpf)) {
            throw new \Exception("CPF inválido");
        }
        $this->cpf = $formated_cpf;
    }
    
    /**
     * Método buscaInfoCPF
     * Método para fazer a requisição e alementar a propriedade results_string
     * @param string $outros_parametros
     * Parâmetro para colocar varáveis extra na url de chamada
     * @return void;
      * @throws Exception
     */

    public function buscaInfoCPF($dados_cpf, $data ='')
    {
        $result = ['success' => false, 'message' => ['error' => 'CPF não encontrado.']];
        try {
            $url = self::CPF_SITE . '/' . self::CPF_TOKEN . '/' . self::CPF_CODCONSULTA . '/' . $dados_cpf;
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              $result = ['success' => false, 'message' => ['error' => $err]];
            } else {
                $array =  json_decode($response, true);
                if(!$array['status']){
                    throw new \Exception($array['erro'], 1);
                }

                $result = [
                    'success' => ($array['status']),
                    'data' => [
                        'cpf' => $array['cpf'],
                        'nome_completo' => $array['nome'],
                        'data_nascimento' => $array['nascimento'],
                        'nome_mae' => $array['mae'],
                        'genero' => $array['genero'],
                    ],
                    'success' => ($array['status']),
                ];
            }
            
        } catch (\Exception $e) {
            $result['message'] = ['error' => $e->getMessage()];
        }
        return $result;
    }
    
    
    public function retornaConteudoRequisicao()
    {  
        if($this->results_string == ""){
            throw new \Exception('Não houve requisição através do método retornaCPF');
        }
        return $this->results_string;
    }
}