<?php 
session_start();
if(!isset($_COOKIE["ID_UTILISATEUR"])){
	header("Location: ../../../logout.php");
	exit;
}
include_once("../../../header.inc.php");
include_once("../../../include/functions.php");
$idForCal=trim(dechiffre(hex2bin("$_GET[awq]"), "$_SESSION[UNIQID]"));
$typeForCal=trim(dechiffre(hex2bin("$_GET[zxs]"), "$_SESSION[UNIQID]"));
//echo "$idForCal ** $typeForCal ** $_SESSION[UNIQID]";
if ($typeForCal=='user'){
	if ($idForCal!=$_COOKIE[ID_UTILISATEUR]){echo "Accès non autorisé";exit;}
}elseif ($typeForCal=='enfant'){
	$nbEnfant=0;	
	connectSQL();
	$req="SELECT a.id FROM enfant a, contrat b WHERE ((a.id_parent1=$_COOKIE[ID_UTILISATEUR] OR a.id_parent2=$_COOKIE[ID_UTILISATEUR]) AND a.id=$idForCal) 
	OR (b.id_asm=$_COOKIE[ID_UTILISATEUR] AND b.id_enfant=$idForCal)";		
	$rec=@mysql_query($req);	
	//echo $req;
	if (@mysql_num_rows($rec)<1){echo "Accès non autorisé";exit;}
}
$idForCal=bin2hex(chiffre("$idForCal", "$_SESSION[UNIQID]"));
$typeForCal=bin2hex(chiffre("$typeForCal", "$_SESSION[UNIQID]"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title><?php  echo $GLOBALS['params']['appli']['title_appli'];?> - Mon agenda </title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link href="css/dailog.css" rel="stylesheet" type="text/css" />
    <link href="css/calendar.css" rel="stylesheet" type="text/css" /> 
    <link href="css/dp.css" rel="stylesheet" type="text/css" />   
    <link href="css/alert.css" rel="stylesheet" type="text/css" /> 
    <link href="css/main.css" rel="stylesheet" type="text/css" /> 
    

    <script src="src/jquery.js" type="text/javascript"></script>  
    
    <script src="src/Plugins/Common.js" type="text/javascript"></script>    
    <script src="src/Plugins/datepicker_lang_FR.js" type="text/javascript"></script>     
    <script src="src/Plugins/jquery.datepicker.js" type="text/javascript"></script>

    <script src="src/Plugins/jquery.alert.js" type="text/javascript"></script>    
    <script src="src/Plugins/jquery.ifrmdailog.js" defer="defer" type="text/javascript"></script>
    <script src="src/Plugins/wdCalendar_lang_FR.js" type="text/javascript"></script>    
    <script src="src/Plugins/jquery.calendar.js" type="text/javascript"></script>   
    
    <script type="text/javascript">
        $(document).ready(function() {     
           var view="week";          
           
            var DATA_FEED_URL = "php/datafeed.php";
            var op = {
                view: view,
                theme:6,
                showday: new Date(),
                EditCmdhandler:Edit,
                DeleteCmdhandler:Delete,
                ViewCmdhandler:View,    
                onWeekOrMonthToDay:wtd,
                onBeforeRequestData: cal_beforerequest,
                onAfterRequestData: cal_afterrequest,
                onRequestDataError: cal_onerror, 
                autoload:true,
                url: DATA_FEED_URL + "?awq=<?php echo $idForCal;?>&zxs=<?php echo $typeForCal;?>&method=list",  
                quickAddUrl: DATA_FEED_URL + "?awq=<?php echo $idForCal;?>&zxs=<?php echo $typeForCal;?>&method=add", 
                quickUpdateUrl: DATA_FEED_URL + "?awq=<?php echo $idForCal;?>&zxs=<?php echo $typeForCal;?>&method=update",
                quickDeleteUrl: DATA_FEED_URL + "?awq=<?php echo $idForCal;?>&zxs=<?php echo $typeForCal;?>&method=remove"        
            };
            var $dv = $("#calhead");
            var _MH = document.documentElement.clientHeight;
            var dvH = $dv.height() + 2;
            op.height = _MH - dvH;
            op.eventItems =[];

            var p = $("#gridcontainer").bcalendar(op).BcalGetOp();
            if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
            }
            $("#caltoolbar").noSelect();
            
            $("#hdtxtshow").datepicker({ picker: "#txtdatetimeshow", showtarget: $("#txtdatetimeshow"),
            onReturn:function(r){                          
                            var p = $("#gridcontainer").gotoDate(r).BcalGetOp();
                            if (p && p.datestrshow) {
                                $("#txtdatetimeshow").text(p.datestrshow);
                            }
                     } 
            });
            function cal_beforerequest(type)
            {
                var t="Chargement des données...";
                switch(type)
                {
                    case 1:
                        t="Chargement des données...";
                        break;
                    case 2:                      
                    case 3:  
                    case 4:    
                        t="La requête est en cours ...";                                   
                        break;
                }
                $("#errorpannel").hide();
                $("#loadingpannel").html(t).show();    
            }
            function cal_afterrequest(type)
            {
                switch(type)
                {
                    case 1:
                        $("#loadingpannel").hide();
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $("#loadingpannel").html("Enregistrement effectué !");
                        window.setTimeout(function(){ $("#loadingpannel").hide();},2000);
                    break;
                }              
               
            }
            function cal_onerror(type,data)
            {
                $("#errorpannel").show();
            }
            function Edit(data)
            {
               var eurl="edit.php?id={0}&start={2}&end={3}&isallday={4}&title={1}&awq=<?php echo $idForCal;?>&zxs=<?php echo $typeForCal;?>";   
                if(data)
                {
                    var url = StrFormat(eurl,data);
                    OpenModelWindow(url,{ width: 600, height: 400, caption:"Détails de l'événement",onclose:function(){
                       $("#gridcontainer").reload();
                    }});
                }
            }    
            function View(data)
            {
                var str = "";
                $.each(data, function(i, item){
                    str += "[" + i + "]: " + item + "\n";
                });
                alert(str);
            }    
            function Delete(data,callback)
            {           
                
                $.alerts.okButton="Ok";  
                $.alerts.cancelButton="Cancel";  
                hiConfirm("Voulez-vous vraiment supprimer cet événement ?", 'Confirm',function(r){ r && callback(0);});           
            }
            function wtd(p)
            {
               if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $("#showdaybtn").addClass("fcurrent");
            }
            //to show day view
            $("#showdaybtn").click(function(e) {
                //document.location.href="#day";
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $(this).addClass("fcurrent");
                var p = $("#gridcontainer").swtichView("day").BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }
            });
            //to show week view
            $("#showweekbtn").click(function(e) {
                //document.location.href="#week";
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $(this).addClass("fcurrent");
                var p = $("#gridcontainer").swtichView("week").BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }

            });
            //to show month view
            $("#showmonthbtn").click(function(e) {
                //document.location.href="#month";
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $(this).addClass("fcurrent");
                var p = $("#gridcontainer").swtichView("month").BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }
            });
            
            $("#showreflashbtn").click(function(e){
                $("#gridcontainer").reload();
            });
            
            //Add a new event
            $("#faddbtn").click(function(e) {
                var url ="edit.php?awq=<?php echo $idForCal;?>&zxs=<?php echo $typeForCal;?>";
                OpenModelWindow(url,{ width: 500, height: 400, caption: "Créer un événenment"});
            });
            //go to today
            $("#showtodaybtn").click(function(e) {
                var p = $("#gridcontainer").gotoDate().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }


            });
            //previous date range
            $("#sfprevbtn").click(function(e) {
                var p = $("#gridcontainer").previousRange().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }

            });
            //next date range
            $("#sfnextbtn").click(function(e) {
                var p = $("#gridcontainer").nextRange().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }
            });
            
        });
    </script>    
</head>
<body>
    <div>

      <div id="calhead" style="padding-left:1px;padding-right:1px;">          
            <div class="cHead"><div class="ftitle">Mon agenda</div>
            <div id="loadingpannel" class="ptogtitle loadicon" style="display: none;">Chargement des données...</div>
             <div id="errorpannel" class="ptogtitle loaderror" style="display: none;">Désolé, aucune donnée n'a pu être chargée. Merci de réessayer ultérieurement</div>
            </div>          
            
            <div id="caltoolbar" class="ctoolbar">
              <div id="faddbtn" class="fbutton">
                <div><span title='Cliquer pour créer un nouvel événement' class="addcal">

                Nouvel événement                
                </span></div>
            </div>
            <div class="btnseparator"></div>
             <div id="showtodaybtn" class="fbutton">
                <div><span title="Cliquer pour retourner à aujourd'hui" class="showtoday">
                Aujourd'hui</span></div>
            </div>
              <div class="btnseparator"></div>

            <div id="showdaybtn" class="fbutton">
                <div><span title='Jour' class="showdayview">Jour</span></div>
            </div>
              <div  id="showweekbtn" class="fbutton fcurrent">
                <div><span title='Semaine' class="showweekview">Semaine</span></div>
            </div>
              <div  id="showmonthbtn" class="fbutton">
                <div><span title='Mois' class="showmonthview">Mois</span></div>

            </div>
            <div class="btnseparator"></div>
              <div  id="showreflashbtn" class="fbutton">
                <div><span title='Rafraîchir' class="showdayflash">Rafraîchir</span></div>
                </div>
             <div class="btnseparator"></div>
            <div id="sfprevbtn" title="Précédent"  class="fbutton">
              <span class="fprev"></span>

            </div>
            <div id="sfnextbtn" title="Suivant" class="fbutton">
                <span class="fnext"></span>
            </div>
            <div class="fshowdatep fbutton">
                    <div>
                        <input type="hidden" name="txtshow" id="hdtxtshow" />
                        <span id="txtdatetimeshow">Choisir un date</span>

                    </div>
            </div>
            
            <div class="clear"></div>
            </div>
      </div>
      <div style="padding:1px;">

        <div class="t1 chromeColor">
            &nbsp;</div>
        <div class="t2 chromeColor">
            &nbsp;</div>
        <div id="dvCalMain" class="calmain printborder">
            <div id="gridcontainer" style="overflow-y: visible;">
            </div>
        </div>
        <div class="t2 chromeColor">

            &nbsp;</div>
        <div class="t1 chromeColor">
            &nbsp;
        </div>   
        </div>
     
  </div>
    
</body>
</html>
