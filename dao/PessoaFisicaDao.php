<?php

class PessoaFisicaDao{

	public function verDados($cpf){
			require_once('util/ServiceDB.php');
			$servicedb = new ServiceDB();
			$cf = ConnectionFactory::singleton();
			try{
				$sql = "SELECT * FROM pessoa_fisica WHERE `PES_CPF` = '$cpf' ORDER BY `PES_NOME` LIMIT 1";
				$query = $servicedb->ExecutarSQL($sql, $cf->conn);
				$row = mysql_fetch_assoc($query);
				return $row;
			} catch ( PDOException $ex ){ echo "Erro: ".$ex->getMessage(); }
	}
	
	//verifica dados para efetuar login
	function verDadosLogin($email,$senha) {
		require_once('../util/ServiceDB.php');
		$servicedb = new ServiceDB();
		$cf = ConnectionFactory::singleton();
			
		
		session_start();
		$_SESSION['id'] = "";
		$_SESSION['nome'] = "";
		$_SESSION['id_status'] = "";
		try{
			$sql = "SELECT * FROM pessoa_fisica WHERE PES_EMAIL1 = '$email' AND PES_SENHA = '$senha' ORDER BY PES_NOME LIMIT 1";	
			$query = $servicedb->ExecutarSQL($sql, $cf->conn);
			
			$linha= $servicedb->NumRows($query);
														
			if($linha){
			
				while($linha = mysql_fetch_object($query)){
					$_SESSION['id'] = $linha->PES_CPF;
					$_SESSION['nome'] = $linha->PES_NOME;
				}
				return "http://www.abla.com.br/wp-content/themes/abla/abla/alterarFormularioPF";		
									
			}else{
	
				$sql = "SELECT * FROM pessoa_juridica WHERE PES_EMAIL1 = '$email' AND PES_SENHA = '$senha' ORDER BY PES_NOME LIMIT 1";
				$query = $servicedb->ExecutarSQL($sql, $cf->conn);
			
				$linha= $servicedb->NumRows($query);
														
				if($linha){
			
					while($linha = mysql_fetch_object($query)){
						$_SESSION['id'] = $linha->PES_CNPJ;
						$_SESSION['nome'] = $linha->PES_RAZAO_SOC;
						$_SESSION['id_status'] = $linha->PES_ID_STATUS;
 						$_SESSION['login'] = $linha->PES_EMAIL1;
						
						$id = $linha->PES_CNPJ;
						$status = $linha->PES_ID_STATUS;
						$login = $linha->PES_EMAIL1;
						$nome_locadora = $linha->PES_RAZAO_SOC;
						setcookie("cookie-locadora", $nome_locadora, time()+3600, "/privado", "abla.com.br", 0);
 						setcookie("cookie-status", $status, time()+3600, "/privado", "abla.com.br", 0);
						setcookie("cookie-id", $id, time()+3600, "/privado", "abla.com.br", 0);
						setcookie("cookie-login", $login, time()+3600, "/privado", "abla.com.br", 0);



					}
					return "http://www.abla.com.br/"; 
					//return "http://www.abla.com.br/wp-content/themes/abla/abla/pessoaJuridica";
				}else{
					return null;
				}
				
			}
		}catch ( PDOException $ex ){
			 echo "Erro: ".$ex->getMessage(); 
		}
	}
		
	public function verDadosLembrarSenha($cnpCpf){
			require_once('../util/ServiceDB.php');
			$servicedb = new ServiceDB();
			$cf = ConnectionFactory::singleton();
			try{
				$sql = "SELECT PES_SENHA,PES_EMAIL1,PES_EMAIL2  FROM pessoa_juridica WHERE PES_CNPJ = '$cnpCpf' LIMIT 1";
					$query = $servicedb->ExecutarSQL($sql, $cf->conn);
					$row= $servicedb->NumRows($query);
					
					if($row == 1){
						$row = mysql_fetch_assoc($query);
						return $row;	
							
				}else{
					$sql = "SELECT PES_SENHA,PES_EMAIL1 FROM pessoa_fisica WHERE PES_CPF = '$cnpCpf' LIMIT 1";
				$query = $servicedb->ExecutarSQL($sql, $cf->conn);
				$row= $servicedb->NumRows($query);
				
				if($row == 1){
					$row = mysql_fetch_assoc($query);
					return $row;							
					}else{
						return false;
					}
				}
			} catch ( PDOException $ex ){ echo "Erro: ".$ex->getMessage(); }
	}
	public function verDadosLembrarSenhaEmail($email){
			require_once('../util/ServiceDB.php');
			$servicedb = new ServiceDB();
			$cf = ConnectionFactory::singleton();
			try{
				$sql = "SELECT PES_SENHA FROM pessoa_fisica WHERE PES_EMAIL1 = '$email' LIMIT 1";
				$query = $servicedb->ExecutarSQL($sql, $cf->conn);
				$linha= $servicedb->NumRows($query);
				
				if($linha == 1){
					$linha = mysql_fetch_assoc($query);
					return $linha['PES_SENHA'];				
				}else{
					$sql = "SELECT PES_SENHA FROM pessoa_juridica WHERE PES_EMAIL1 = '$email' LIMIT 1";
					$query = $servicedb->ExecutarSQL($sql, $cf->conn);
					$linha= $servicedb->NumRows($query);
					
					if($linha == 1){
						$linha = mysql_fetch_assoc($query);
						return $linha['PES_SENHA'];							
					}else{
						return false;
					}
				}
			} catch ( PDOException $ex ){ echo "Erro: ".$ex->getMessage(); }
	}
	public function criaPessoaFisica($imagem_dir,$pes_nome, $pes_cpf, $pes_rg, $pes_orgao_exped, $pes_dt_nasc, $pes_sexo, $pes_cel1, $pes_cel2, $pes_email1, $pes_email2, $pes_ocupacao, 
									$pes_grau_escolar, $pes_lin_estrang_out, $pes_qualif_prof, $pes_form_tecnica, $pes_form_superior, $pes_cursos_prof, $pes_anos_exper,
									$pes_soft_loc,$pes_senha,$pes_hr_conect_trab,$pes_brows_out,$pes_voip_out,$pes_rsocial_reg_out,$pes_rsocial_pref_out,$pes_fer_busca_out,$pes_cnpj,$acao){
			
		require_once('../util/ServiceDB.php');
		$servicedb = new ServiceDB();
		$cf = ConnectionFactory::singleton();
		
		try{	$newdate = implode(preg_match("~\-~",$pes_dt_nasc) == 0 ? "-" : "-", array_reverse(explode(preg_match("~\-~", $pes_dt_nasc) == 0 ? "-" : "-", $pes_dt_nasc)));
			$dbdate = trim($newdate);			
			if($acao == 'insert'){
			
				$sql = "SELECT * FROM pessoa_fisica WHERE PES_CPF = '$pes_cpf'";
				$query = $servicedb->ExecutarSQL($sql, $cf->conn);
				if($servicedb->NumRows($query) > 0){
					return "CPF j� cadastrado;";
				}
			
				$sql = "INSERT INTO pessoa_fisica (
										PES_FOTO,
										PES_NOME, 
										PES_CPF,
										PES_RG,
										PES_ORGAO_EXPED, 
										PES_DT_NASC,
										PES_SEXO,
										PES_CEL1, 
										PES_CEL2,
										PES_EMAIL1,
										PES_EMAIL2,
										PES_CNPJ,
										PES_OCUPACAO,	
										PES_GRAU_ESCOLAR,	 
										PES_LIN_ESTRANG_OUT,
										PES_QUALIF_PROF,
										PES_FORM_TECNICA,
										PES_FORM_SUPERIOR,
										PES_CURSOS_PROF,
										PES_ANOS_EXPER,
										PES_SOFT_LOC,
										PES_SENHA,
										PES_HR_CONECT_TRAB,
										PES_BROWS_OUT,
										PES_VOIP_OUT,
										PES_RSOCIAL_REG_OUT,
										PES_RSOCIAL_PREF_OUT,
										PES_FER_BUSCA_OUT,
										PES_DATA_CADASTRO
									)values(
										'$imagem_dir',
										'$pes_nome', 
										'$pes_cpf',
										'$pes_rg',
										'$pes_orgao_exped', 
										'$dbdate',
										'$pes_sexo',
										'$pes_cel1', 
										'$pes_cel2',
										'$pes_email1',
										'$pes_email2',
										'$pes_cnpj',
										'$pes_ocupacao',
										'$pes_grau_escolar',
										'$pes_lin_estrang_out',
										'$pes_qualif_prof',
										'$pes_form_tecnica',
										'$pes_form_superior',
										'$pes_cursos_prof',
										'$pes_anos_exper',
										'$pes_soft_loc',
										'$pes_senha',
										'$pes_hr_conect_trab',
										'$pes_brows_out',
										'$pes_voip_out',
										'$pes_rsocial_reg_out',
										'$pes_rsocial_pref_out',
										'$pes_fer_busca_out',
										now()
									)";	
								
				$query = $servicedb->ExecutarSQL($sql, $cf->conn);
				
				if($query){
					return true;
				}else{
					return "Erro ao realizar cadastro.";
				}
			
			}else{
			
				$sql = "UPDATE pessoa_fisica SET
									PES_FOTO = '$imagem_dir',
									PES_NOME = '$pes_nome', 
									PES_RG = '$pes_rg',
									PES_ORGAO_EXPED = '$pes_orgao_exped', 
									PES_DT_NASC = '$pes_dt_nasc',
									PES_SEXO = '$pes_sexo',
									PES_CEL1 = '$pes_cel1', 
									PES_CEL2 = '$pes_cel2',
									PES_EMAIL1 = '$pes_email1',
									PES_EMAIL2 = '$pes_email2',
									PES_CNPJ = '$pes_cnpj',
									PES_OCUPACAO = '$pes_ocupacao',	
									PES_GRAU_ESCOLAR = '$pes_grau_escolar',	 
									PES_LIN_ESTRANG_OUT = '$pes_lin_estrang_out',
									PES_QUALIF_PROF = '$pes_qualif_prof',
									PES_FORM_TECNICA = '$pes_form_tecnica',
									PES_FORM_SUPERIOR = '$pes_form_superior',
									PES_CURSOS_PROF = '$pes_cursos_prof',
									PES_ANOS_EXPER = '$pes_anos_exper',
									PES_SOFT_LOC = '$pes_soft_loc',
									PES_HR_CONECT_TRAB = '$pes_hr_conect_trab',
									PES_BROWS_OUT = '$pes_brows_out',
									PES_VOIP_OUT = '$pes_voip_out',
									PES_RSOCIAL_REG_OUT = '$pes_rsocial_reg_out',
									PES_RSOCIAL_PREF_OUT = '$pes_rsocial_pref_out',
									PES_FER_BUSCA_OUT = '$pes_fer_busca_out'
								WHERE PES_CPF = '$pes_cpf'
								";	
								
				$query = $servicedb->ExecutarSQL($sql, $cf->conn); 
				
				if($query){
					return "Altera��o realizada com sucesso.";
				}else{
					return "Erro ao realizar altera��o.";
				}
				
			}

			/*$sql_cons = "select PES_CARGO from pessoas where PES_CPF = ".$pes_cpf;
			$query_cons = $servicedb->ExecutarSQL($sql_cons, $cf->conn);
			$cargo = $servicedb->Result($query_cons, 0, "PES_CARGO");
			echo " <br><br>Resultado do BD: ";
			print_r($cargo);
			
			$sql_lin = "select PES_LIN_ESTRANG from pessoas where PES_CPF = ".$pes_cpf;
			$query_lin = $servicedb->ExecutarSQL($sql_lin, $cf->conn);
			$lingua = $servicedb->Result($query_lin, 0, "PES_LIN_ESTRANG");
			echo " <br><br>Resultado do BD: ";
			print_r($lingua);*/

		}catch ( PDOException $ex ){ echo "Erro: ".$ex->getMessage(); }
	}

	
	public function alteraSenha($pes_cpf, $pes_email1, $pes_senha){
			
		require_once('../util/ServiceDB.php');
		$servicedb = new ServiceDB();
		$cf = ConnectionFactory::singleton();
		
		try{	
			$sql = "UPDATE pessoa_fisica SET 
						PES_SENHA = '$pes_senha'
					WHERE PES_CPF = '$pes_cpf' AND
						PES_EMAIL1 = '$pes_email1'
					";	
							
			$query = $servicedb->ExecutarSQL($sql, $cf->conn);
			
			if($query){
				return "Altera��o de senha realizada com sucesso.";
			}else{
				return "Erro ao realizar altera��o de senha.";
			}
		}catch ( PDOException $ex ){ echo "Erro: ".$ex->getMessage(); }
	}
	public function relatorio(){
			require_once('util/ServiceDB.php');
			$servicedb = new ServiceDB();
			$cf = ConnectionFactory::singleton();
			try{
				$sql = "SELECT pessoa_fisica.PES_CPF, `PES_NOME`, `PES_RG`, `PES_ORGAO_EXPED`, `PES_DT_NASC`, `PES_SEXO`, `PES_CEL1`, `PES_CEL2`, `PES_EMAIL1`,
						`PES_EMAIL2`,`LOGRAD_RESID` , `NUM_RESID` , `COMPL_RESID` , `BAIRRO_RESID` , cidade.cidade, sigla, `CEP_RESID` ,
						`PES_OCUPACAO`, CAR_NOME, `GRAU_NOME`,IDIOMA_NOME, FLUENCIA,`PES_LIN_ESTRANG_OUT`,`PES_QUALIF_PROF`, `PES_FORM_TECNICA`,
						`PES_FORM_SUPERIOR`, `PES_CURSOS_PROF`, `PES_ANOS_EXPER`, `PES_SOFT_LOC`, `PES_HR_CONECT_TRAB`, BROW_NOME, `PES_BROWS_OUT`,
						`VOIP_NOME`,`PES_VOIP_OUT`,REDE_NOME, `PES_RSOCIAL_REG_OUT`, `PES_DATA_CADASTRO`
						FROM pessoa_fisica
						LEFT JOIN endereco ON endereco.PES_CPF = pessoa_fisica.PES_CPF
						LEFT JOIN cidade ON endereco.CIDADE_RESID = cidade.id
						LEFT JOIN estado ON cidade.estado = estado.id
						LEFT JOIN cargo ON cargo.PES_CPF = pessoa_fisica.PES_CPF
                                          LEFT JOIN cargo_nome ON cargo_nome.CARGO = cargo.CARGO
                                          LEFT JOIN escolaridade ON escolaridade.PES_GRAU_ESCOLAR = pessoa_fisica.PES_GRAU_ESCOLAR
						LEFT JOIN idiomas_falados ON idiomas_falados.PES_CPF = pessoa_fisica.PES_CPF
						LEFT JOIN idiomas ON idiomas_falados.IDIOMA_ID = idiomas.IDIOMA_ID  
						LEFT JOIN browser_utilizado ON browser_utilizado.PES_CPF = pessoa_fisica.PES_CPF
						LEFT JOIN browsers ON browser_utilizado.BROW_ID = browsers.BROW_ID
						LEFT JOIN voip_utilizado ON voip_utilizado.PES_CPF = pessoa_fisica.PES_CPF
						LEFT JOIN voip ON voip_utilizado.VOIP_ID = voip.VOIP_ID
						LEFT JOIN redes_utilizadas ON redes_utilizadas.PES_CPF = pessoa_fisica.PES_CPF
						LEFT JOIN redes_sociais ON redes_utilizadas.REDE_ID = redes_sociais.REDE_ID
						GROUP BY pessoa_fisica.PES_CPF
						ORDER BY pessoa_fisica.PES_DATA_CADASTRO DESC";
				$query = $servicedb->ExecutarSQL($sql, $cf->conn);
				
				return $query;
			} catch ( PDOException $ex ){ echo "Erro: ".$ex->getMessage(); }
	 }
}
