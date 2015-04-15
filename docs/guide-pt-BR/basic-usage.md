Uso Básico
===========

Quando você abre Gii, você verá primeiro a página de entrada que lhe permite escolher um gerador.

![Gii entry page](images/gii-entry.png)

Por padrão, existem os seguintes geradores disponíveis:

- **Model Generator** - Este gerador gera uma classe ActiveRecord para a tabela de banco de dados especificada.
- **CRUD Generator** - Este gerador gera um controlador e views que implementam CRUD (Create, Read, Update, Delete ou Criar, Recuperar, Atualizar e Deletar) das 
	operações para o modelo de dados especificado.
- **Controller Generator** - Este gerador ajuda a gerar rapidamente uma nova classe de controlador, um ou mais
	ações do controlador para as views correspondentes.
- **Form Generator** - Este gerador gera um arquivo view que exibe um formulário para receber dados para a
	classe de modelo especificado.
- **Module Generator** - Este gerador ajuda a gerar o esqueleto necessário por um módulo Yii.
- **Extension Generator** - Este gerador ajuda a gerar os arquivos necessários por uma extensão Yii.

Depois de escolher um gerador, clicando no botão "Start", você verá um formulário que permite que você configure os
parâmetros do gerador. Preencha o formulário de acordo com as suas necessidades e pressione o botão "Preview" para obter uma
pré-visualização do código que o GII está prestes a gerar. Dependendo do gerador que você escolheu e/ou se os arquivos
já existiam ou não, você vai ter uma tela semelhante ao que você vê na figura abaixo:

![Gii preview](images/gii-preview.png)

Clicando sobre o nome do arquivo que você poderá ver um preview do código que será gerado para o arquivo.
Quando o arquivo já existe, o Gii fornece uma visão diff que mostra o que é diferente entre o código que existe
e o que vai ser gerado. Neste caso, você também pode escolher quais arquivos devem ser substituídos.

> Dica: Ao utilizar o Modelo Generator para atualizar modelos após a mudança de banco de dados, você pode copiar o código da pré-visualização do Gii 
  e mesclar as alterações com o seu próprio código. Você pode usar recursos de uma IDE como PHPStorms.
  [compare with clipboard](http://www.jetbrains.com/phpstorm/webhelp/comparing-files.html), [Aptana Studio](http://www.aptana.com/products/studio3/download) ou [Eclipse](http://www.eclipse.org/pdt/) based também permitem [compare with clipboard](http://andrei.gmxhome.de/anyedit/examples.html) por usar [AnyEdit tools plugin](http://andrei.gmxhome.de/anyedit/) para isso, que lhe permite unir as mudanças relevantes e deixar de fora outros que podem reverter o seu próprio código.
  
Depois de revisar o código e selecionar os arquivos a serem gerados você pode clicar no botão "Gerar" para criar
os arquivos. Se tudo correu bem está pronto. Quando você vê erros que Gii não é capaz de gerar os arquivos e que você tem que
ajustar as permissões do diretório do seu servidor web permitindo ser capaz de escrever para os diretórios desejados e para criar os arquivos.

> Nota: O código gerado pelo Gii é apenas um template que tem que ser ajustado às suas necessidades. Isto é uma ajuda 
  para você criar novas coisas mais rapidamente mas não é algo que cria código pronto para uso.
  Muitas vezes vemos pessoas que utilizam os modelos gerados pelo GII, sem alteração e basta apenas estendê-los para ajustar
  algumas partes dele. Esta não é a forma para ele ser usado. Código gerado pelo GII é incompleto, ou incorreto, de deve ser alterado para atender  às suas necessidades de que que você possa usa-lo realmente.
  