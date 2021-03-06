<?PHP
if (!empty($_SESSION['MESSAGE'])){unset($_SESSION['MESSAGE']);}
if(isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: menu.php");
}
include_once("header.inc.php");
include_once("include/functions.php");
include_once("include/protect_var.php");
entete_page($GLOBALS['params']['appli']['title_appli']." - Connexion",'');
print_r($message);

?>
<script type="text/javascript" src="content/jquery-1.7.2.min.js"></script> 
<script type="text/javascript" src="content/jquery.validate.min.js"></script>
<script type="text/javascript" src="content/tooltip/jquery.tools.min.js"></script>
<script type="text/javascript" src="content/winDim.js"></script>
<script type="text/javascript" src="content/highslide/highslide-full.min.js"></script>
<script type="text/javascript" src="content/highslide/highslide-with-html.js"></script>
<script type="text/javascript" src="content/highcharts/highslide/highslide.config.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="content/highslide/highslide.css" />

<!--<link type="text/css" href="content/jquery-ui/css/start/jquery-ui-1.8.6.custom.css" rel="Stylesheet" />
<link rel="stylesheet" type="text/css" href="content/connexion.css" />-->

<script type="text/javascript">
	var normalLeft = 0;
	var normalRight = 0;		
	var normalWidth = 0;
	var normalHeight = 0;	
    hs.graphicsDir = 'content/highslide/graphics/';
    hs.outlineType = 'rounded-white';
	hs.registerOverlay({
		//html: '<div class="highslide-header"><ul><li class="highslide-restore"><a href="#" title="Restaurer" onclick="return hs.restore(this)"></a></li><li class="highslide-maximize"><a href="#" title="Agrandir" onclick="return hs.maximize(this)"></a></li></ul></div><div class="closebutton" onclick="return hs.close(this)" title="Fermer"></div>',
		html: '<div class="moveMenu"><div class="highslide-maximize" title="Agrandir" onclick="return hs.maximize(this)"></div><div class="highslide-space"></div><div class="highslide-restore" title="Restaurer" onclick="return hs.restore(this)"></div></div><div class="closebutton" onclick="return hs.close(this)" title="Fermer"></div>',
		position: 'top right',
		fade: 2,
		useOnHtml: true
	});	
	hs.maximize = function(el) {
		var exp = hs.getExpander(el);		
		normalWidth = exp.x.size;
		normalHeight = exp.y.size;		
		hs.getPageSize();	
		 for (i = 0; i < hs.expanders.length; i++) {
		      exp = hs.expanders[i];
		      if (exp) {
		         var x = exp.x,
		            y = exp.y;

		         // get new thumb positions
		         exp.tpos = hs.getPosition(exp.el);
		         x.calcThumb();
		         y.calcThumb();

		         // calculate new popup position
		         x.pos = x.tpos - x.cb + x.tb;
		         x.scroll = hs.page.scrollLeft;
		         x.clientSize = hs.page.width;
		         y.pos = y.tpos - y.cb + y.tb;
		         y.scroll = hs.page.scrollTop;
		         y.clientSize = hs.page.height;
		         exp.justify(x, true);
		         exp.justify(y, true);

		         // set new left and top to wrapper and outline
		         //exp.moveTo(x.pos, y.pos);
		         normalLeft = x.pos;
		         normalRight = y.pos;
		      }
		   }		
		exp.moveTo (0, 14);
		exp.resizeTo(hs.page.width - 15, hs.page.height - 25);	
		return false;
	}
	hs.restore = function(el) {		
		var exp = hs.getExpander(el);				
		hs.getPageSize();		
		exp.moveTo (normalLeft, normalRight);	
		exp.resizeTo(normalWidth, normalHeight);
		hs.align = 'center';		
		return false;
	}
	//hs.Expander.prototype.onBeforeClose = function (sender) {}								
	hs.Expander.prototype.printIframe = function () {
	   var name = this.iframe.name;
	   frames[name].focus();
	   frames[name].print();
	   return false;
	}
	hs.Expander.prototype.printHtml = function ()
	{
	    var pw = window.open("about:blank", "_new");
	    pw.document.open();
	    pw.document.write(this.getHtmlPrintPage());
	    pw.document.close();
	    return false;
	};
	hs.Expander.prototype.getHtmlPrintPage = function()
	{
	    // We break the closing script tag in half to prevent
	    // the HTML parser from seeing it as a part of
	    // the *main* page.
	    var body = hs.getElementByClass(this.innerContent, 'DIV', 'highslide-body')
	        || this.innerContent;

	    return "<html>\n" +
	        "<head>\n" +
	        "<title>Temporary Printing Window</title>\n" +
	        "<script>\n" +"function step1() {\n" +
	        "  setTimeout('step2()', 10);\n" +
	        "}\n" +
	        "function step2() {\n" +
	        "  window.print();\n" +
	        "  window.close();\n" +
	        "}\n" +
	        "</scr" + "ipt>\n" +
	        "</head>\n" +
	        "<body onLoad='step1()'>\n"
	        +body.innerHTML +
	        "</body>\n" +
	        "</html>\n";
	};
	jQuery(document).ready(function(){	
		jQuery("input").focus(function() {
			jQuery("#messAcc").hide();
		});	
		jQuery("#myForm").validate({
			debug: false,
			rules: {								
				login: {
					required: true,
					minlength: 3,
					maxlength: 20
				},
				pass: {
					required: true,
					minlength: 8
				}				
			},
			messages: {
				login: {
					required: "Vous devez renseigner un nom d'utilisateur",
					minlength: "Le nom d'utilisateur doit comporter plus de 2 caractères",
					maxlength: "Le nom d'utilisateur doit comporter moins de 20 caractères"
				},
				pass: {
					required: "Vous devez renseigner un mot de passe",
					minlength: "Le mot de passe doit comporter au moins 8 caractères"					
				}
			},
			submitHandler: function(form) {			
				var datas = jQuery(form).serialize();	
				jQuery("#messAcc").hide();				
	            jQuery.ajax({
	                cache: false,
	                type: 'POST',
	                data: datas,
	                url : 'login.php',
	                success: function (response) {                    
	                    jQuery("#messAcc").attr('class','messAcc');
	                    jQuery("#messAcc").show(1500);
	                    jQuery("#messAcc").html(response);	                    
	                    if (response==''){
	                    	 window.location.href = 'menu.php';			                    
	                    }
	                },
	                error: function(data, textStatus, jqXHR) {
	                	jQuery("#messAcc").attr('class','messErrAcc');
	                    jQuery("#messAcc").show(1500);
	                    jQuery("#messAcc").html(data);                	
	                }
	            })
			}
		});		
	});
</script>
<style type="text/css">
.highslide-maximize {	
	position: absolute;	
	margin-left: 16px;		
	width: 12px;
	height: 8px;
	border: 2px solid #aaaaaa;
}
.highslide-restore {	
	position: absolute;		
	width: 5px;
	height: 8px;
	border: 2px solid #aaaaaa;
	/*border: 2px solid #ff0000;*/
}
.highslide-space {	
	position: absolute;	
	margin-left: 14px;
	width: 15px;
	height: 4px;	
	/*border: 2px solid #ff0000;*/
}
html, body{height: 100%;width:100%;margin: 0; padding: 0;}
</style>
</head>
<body onLoad="javascript: window.focus(); document.form1.login.focus();">
<div class="highslide-html-content" id="highslide-html<?php echo $page;?>">
	<div class="highslide-header">	
	</div>
	<div class="highslide-body">
		Pour vous connecter sur ce site vous devez disposer d'un compte valide. <br/><br/>
		<b><i>J'ai déjà un compte</i></b> <br/>						
		Saisissez le nom d'utilisateur et le mot qui vous ont été fourni et cliquez sur le bouton "Connexion".<br/>	<br/>
		<b><i>Je n'ai pas de compte</i></b> <br/>
		Cliquez sur le bouton "Créer un compte", une nouvelle fenêtre s'ouvre vous demandant des renseignements de base<br/>
		Saisissez les informations demandées.<br/><br/>
		Notez que vous devriez utiliser un mot de passe comportant <b>au moins 8 caratères</b> mélangeant des chiffres et des lettres.<br/><br/>
		Une fois les informations saisies, cliquez sur "Valider".
		<br/><br/>
		<b><i>J'ai perdu mon mot de passe</i></b><br/>	
		Cliquez sur le lien "Mot de passe oublié" et renseignez l'adresse mail que vous avez utilisé lors de la création de votre compte.<br/>
		Un mail vous sera envoyé avec un nouveau mot de passe, il est impératif de cliquer sur le lien présent dans ce dernier. Auquel cas votre mot de passe ne serait pas modifié.
		<br/><br/>
		<b><i>J'ai perdu mon identifiant</i></b><br/>
		Cliquez sur le lien "Identifiant oublié" et renseignez l'adresse mail que vous avez utilisé lors de la création de votre compte.<br/>
		Un mail vous sera envoyé avec l'information demandée.
	</div>

    <div class="highslide-footer">
        <div>
            <span class="highslide-resize" title="Resize">
                <span></span>
            </span>
        </div>
    </div>
</div>
<div class="highslide-html-content" id="highslide-html-version">
	<div class="highslide-header">	
	</div>
	<div class="highslide-body">
		 <a class="position" href="docs/versions/note_version_0.1.pdf">Version 0.1</a><br/>		 
	</div>

    <div class="highslide-footer">
        <div>
            <span class="highslide-resize" title="Resize">
                <span></span>
            </span>
        </div>
    </div>
</div>
<div class="highslide-html-content" id="highslide-html-ml">
	<div class="highslide-header">	
	</div>
	<div class="highslide-body">
		 <?php include('include/ml.html');?>		 
	</div>

    <div class="highslide-footer">
        <div>
            <span class="highslide-resize" title="Resize">
                <span></span>
            </span>
        </div>
    </div>
</div>
<table class="table-gene" cellspacing="0">
		<tr>
			<td class="left" rowspan=2>
				<img src="<?php echo $GLOBALS['params']['appli']['image_appli'];?>" />
			</td>
			<td class="middle" rowspan=2>
				<!-- <img class="login-logo" id="login-logo" src="graphs/images/logo_gde.gif" alt="GDE" width=64 height=64/> -->			
				<div class="titre">
					<h1><?php echo $GLOBALS['params']['appli']['titre_haut'];?></h1>
					<h2><?php echo $GLOBALS['params']['appli']['titre_bas'];?></h2>
					<br /><br />
					<span style="white-space:nowrap;">
						<span title="Historique des versions">							
							<a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-version',headingText: 'Notes de version' } )" style="text-decoration: underline;color: #fff;">
							Version <?php echo $GLOBALS['params']['appli']['version'];?></a></span>&nbsp;
						<span></span>
					</span><br /><br />
					<!-- <span>
						<img style="cursor: pointer;"
						onclick="return hs.htmlExpand(null, { 
									src:'public',
									objectType: 'iframe',
									width: 860,							
									headingText: 'GDE - Gestionnaire de fichiers&nbsp;&nbsp;',						
									preservcontent: true } );"	
							 title="Accéder aux fichiers publics" src="graphs/icons/files26.png">
					</span> -->
					<br /><br /><br />
				</div>
			</td>
			<td class="right-logo">				
				<!-- <img src="<?php echo $GLOBALS['params']['appli']['logo_left'];?>" alt="Ministère de l'éducation nationale, de la jeunesse et de la vie associative"> -->				
			</td>
		</tr>
		<tr>
			<td class="right-gde">
				<!-- <a href="http://g-d-e.eu" target="_blank"><img src="<?php echo $GLOBALS['params']['appli']['logo_appli'];?>" alt="GDE"/></a> -->
			</td>
		</tr>
</table>
<div class="connexion">
	<div style="width:20px;float:right;margin-right:30px;margin-top:10px;" title="Aide à la connexion">	
		<a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html<?php echo $page;?>',headingText: 'Aide à la connexion',preserveContent: false } )">
			<img src="graphs/icons/info.png" alt="Aide" border="0"/>
		</a>
	</div>
	<div class="cocc">
		<div style="bottom:5px;left: 5px; position:absolute;"><a href="#" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-ml',headingText: 'Mentions Légales' } )" style="text-decoration: underline;margin-bottom:0;">Mentions Légales</a></div>
		</div>	
	<table style="height:100%;width:50%;float:right;border:0;margin:0;padding:0;" cellspacing="0">		
		<tr>
			<td width="47px"></td>
			<td valign=top>				
				<!-- <form action="login.php" method="post" name="form1">-->
				<form method="post" id="myForm" action="#" name="form1">
				<table cellspacing="0" cellpadding="0" style="width:100%;">
					<tbody>
					<tr>
						<td height="30px" colspan="2"></td>
					</tr>
					<tr>
						<td colspan="2">
							<div style="display: none;"></div>
						</td>
					</tr>
					<tr>
				    	<td colspan="2"><div id="messAcc" style="display: none;"></div></td>
				  	</tr>
					<tr>
						<td style="width: 50px;" class="user">
							<span style="white-space: nowrap; vertical-align: middle;">Utilisateur</span>
						</td>
						<td class="user_input">
							<input type="text" size="15" name="login" class="text_input" autocomplete="off" tabindex=1>						
						</td>
					</tr>
					<tr>
						<td class="user">
							<span style="vertical-align: middle;">Mot&nbsp;de&nbsp;passe</span>							
						</td>
						<td class="user_input">
							<input type="password" size="15" name="pass" class="text_input" autocomplete="off" tabindex=2>								
						</td>
					</tr>					
					<tr>
						<!--<td>&nbsp;</td>-->
						<td colspan=2 style="padding-left: 45px;">
							<br>
							<button type="submit" name="connexion" tabindex="3" value="Connexion" style="cursor: pointer;" class="buttonValid">Connexion</button></form>													
							<button type="submit" name="create" tabindex="3" value="Connexion" style="cursor: pointer;" class="buttonValid" onclick="return hs.htmlExpand(null, {
								src:'resources/create_account.php',
								objectType: 'iframe',
								width: 860,
								height:600,
								headingText: '<?php echo $GLOBALS['params']['appli']['title_appli'];?> - Créer un compte&nbsp;&nbsp;',
								preservcontent: true } )">Créer un compte</button>
						</td>
					</tr>									
					<tr>						
						<td colspan=2 style="padding-left: 5px;">						
							<input type="checkbox" name="connexion_auto" /> Se connecter automatiquement à chaque visite ?		
						</td>				
					</tr>
					<tr>						
						<td colspan=2 style="padding-top: 10px;padding-left: 30px;">	
							<a href="#" style="text-decoration: underline;color: #666666;" onclick="return hs.htmlExpand(this, { src: 'resources/lost_password.php',objectType: 'iframe', headingText: 'Mot de passe oublié',width: 860});"	title="J'ai perdu mon mot de passe">Mot de passe oublié ?</a>					
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" style="text-decoration: underline;color: #666666;" onclick="return hs.htmlExpand(this, { src: 'resources/lost_id.php',objectType: 'iframe', headingText: 'Identifiant oublié',width: 860});" title="J'ai oublié mon identifiant de connexion">Identifiant oublié ?</a>		
						</td>				
					</tr>
				</tbody>
				</table>				
			</td>
		</tr>
		<tr height="26px;">
			<td colspan="2" style="text-align: right;">
				<span>	
				<i>Navigateurs&nbsp;recommandés</i> 			
				<img style="cursor: help;" title="Navigateur recommandé  : Firefox 3.0 ou supérieur" src="graphs/icons/firefox.png" alt="Firefox 3.0 ou supérieur">
	     		<img style="cursor: help;" title="Navigateur recommandé  : Chrome 10.0 ou supérieur" src="graphs/icons/chrome.png" alt="Chrome 10.0 ou supérieur">
				</span>
			</td>
		</tr>
	</table>
</div>
</body></HTML>