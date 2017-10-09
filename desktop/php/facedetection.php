<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'facedetection');
$eqLogics = eqLogic::byType('facedetection');
?>
<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter une camera}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 col-md-7 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">	
		<legend>{{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; " >
				<center>
					<i class="fa fa-plus-circle" style="font-size : 7em;color:#406E88;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#406E88">
					<center>Ajouter</center>
				</span>
			</div>
			<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="height: 120px; margin-bottom: 10px; padding: 5px; border-radius: 2px; width: 160px; margin-left: 10px; position: absolute; left: 170px; top: 0px; background-color: rgb(255, 255, 255);">
				<center>
			      		<i class="fa fa-wrench" style="font-size : 5em;color:#767676;"></i>
			    	</center>
			    	<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>Configuration</center></span>
			</div>
		</div>
        <legend>{{Les caméras surveillées}}</legend>
		<div class="eqLogicThumbnailContainer">
			<?php
			if (count($eqLogics) == 0) {
				echo "<br/><br/><br/><center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>{{Vous n'avez pas encore de camera, cliquez sur Ajouter une camera pour commencer}}</span></center>";
			} else {
			?>
				<?php
				foreach ($eqLogics as $eqLogic) {
					echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
					echo "<center>";
					echo '<img src="plugins/facedetection/doc/images/facedetection_icon.png" height="105" width="95" />';
					echo "</center>";
					echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
					echo '</div>';
				}
				?>
			<?php } ?>
		</div>
    </div>
	<div class="col-lg-10 col-md-7 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-tachometer"></i> Equipement</a>	</li>		
			<li role="presentation" class=""><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-list-alt"></i>Commandes</a></li>
		</ul>
		<div>
			<a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> Sauvegarder</a>
			<a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> Supprimer</a>
			<a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> Configuration avancée</a>
		</div>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab"> 
				<br/>
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-md-5 control-label">{{Nom de l'équipement template}}</label>
						<div class="col-md-7">
							<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
							<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement template}}"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label" >{{Objet parent}}</label>
						<div class="col-md-7">
							<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
								<option value="">{{Aucun}}</option>
								<?php
								foreach (object::all() as $object) {
									echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" ></label>
						<div class="col-sm-9">
							<label>{{Activer}}</label>
							<input type="checkbox" class="eqLogicAttr" data-label-text="{{Activer}}" data-l1key="isEnable"/>
							<label>{{Visible}}</label>
							<input type="checkbox" class="eqLogicAttr" data-label-text="{{Visible}}" data-l1key="isVisible"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">{{Catégorie}}</label>
						<div class="col-md-8">
							<?php
							foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
								echo '<label class="checkbox-inline">';
								echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
								echo '</label>';
							}
							?>

						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">{{Commande d'alerte (mail, slack...)}}</label>
						<div class="col-md-8">
							<div class="input-group">
								<input class="form-control input-sm eqLogicAttr" data-l1key="configuration" data-l2key="alertMessageCommand" placeholder="{{Commande mail pour l'envoi d'une capture}}">
								<span class="input-group-btn">
									<a class="btn btn-success btn-sm listCmdActionMessage" id="bt_selectActionMessage">
										<i class="fa fa-list-alt"></i>
									</a>
								</span>
							</div>
						</div>      
					</div> 
					<div class="form-group">
						<label class="col-md-5 control-label">{{Url de la Camera}}</label>
						<div class="col-md-7 ">
							<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cameraUrl">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">{{Login de connexion a la Camera}}</label>
						<div class="col-md-7 ">
							<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cameraLogin">
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">{{Mots de pass de la Camera}}</label>
						<div class="col-md-7 ">
							<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cameraPass">
						</div>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab"> 
				<a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter une zone de detection}}</a><br/><br/>
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>{{}}</th>
							<th>{{Nom}}</th>
							<th>{{Action}}</th>
							<th>{{}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		</div>
</div>
<?php include_file('desktop', 'facedetection', 'js', 'facedetection'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
