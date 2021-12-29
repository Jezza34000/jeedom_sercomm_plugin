<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('sercomm');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<!-- Page d'accueil du plugin -->
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<!-- Boutons de gestion du plugin -->
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i>
				<br>
				<span>{{Configuration}}</span>
			</div>
		</div>
		<legend><i class="fas fa-table"></i> {{Caméras IP Sercomm}}</legend>
		<?php
		if (count($eqLogics) == 0) {
			echo '<br/><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucune caméra sercomm n\'est paramétré, cliquer sur "Ajouter"}}</div>';
		} else {
			// Champ de recherche
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic"/>';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
			// Liste des équipements du plugin
			echo '<div class="eqLogicThumbnailContainer">';
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
			echo '</div>';
		}

		?>
	</div> <!-- /.eqLogicThumbnailDisplay -->

	<!-- Page de présentation de l'équipement -->
	<div class="col-xs-12 eqLogic" style="display: none;">
		<!-- barre de gestion de l'équipement -->
		<div class="input-group pull-right" style="display:inline-flex;">
			<span class="input-group-btn">
				<!-- Les balises <a></a> sont volontairement fermées à la ligne suivante pour éviter les espaces entre les boutons. Ne pas modifier -->
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
				</a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs">  {{Dupliquer}}</span>
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<!-- Onglets -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>
			<li role="presentation"><a href="#events" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-exclamation"></i><span class="hidden-xs"> {{Déclencheur}}</span></a></li>
			<li role="presentation"><a href="#httpnotify" aria-controls="home" role="tab" data-toggle="tab"><i class="fab fa-chrome"></i><span class="hidden-xs"> {{Notif HTTP}}</span></a></li>
			<li role="presentation"><a href="#mailnotif" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-envelope-open-text"></i><span class="hidden-xs"> {{Email}}</span></a></li>
			<li role="presentation"><a href="#ftpsend" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-file-upload"></i><span class="hidden-xs"> {{FTP}}</span></a></li>
			<li role="presentation"><a href="#motion" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-low-vision"></i><span class="hidden-xs"> {{Mouvement}}</span></a></li>
			<li role="presentation"><a href="#audio" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-microphone"></i><span class="hidden-xs"> {{Audio}}</span></a></li>
			<li role="presentation"><a href="#video" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-video"></i><span class="hidden-xs"> {{Vidéo}}</span></a></li>
			<li role="presentation"><a href="#jpeg" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-camera"></i><span class="hidden-xs"> {{JPEG}}</span></a></li>
			<!---<li role="presentation"><a href="#network" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-network-wired"></i><span class="hidden-xs"> {{Réseau}}</span></a></li>
			<li role="presentation"><a href="#maintenance" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tools"></i><span class="hidden-xs"> {{Maintenance}}</span></a></li>-->
		</ul>
		<div class="tab-content">
			<!-- Onglet de configuration de l'équipement -->
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<!-- Partie gauche de l'onglet "Equipements" -->
				<!-- Paramètres généraux de l'équipement -->
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;"/>
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" >{{Objet parent}}</label>
								<div class="col-sm-7">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
										$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Catégorie}}</label>
								<div class="col-sm-7">
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
								<label class="col-sm-3 control-label">{{Options}}</label>
								<div class="col-sm-7">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
								</div>
							</div>

							<legend><i class="fas fa-cogs"></i> {{Paramètres d'accès à la caméra}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Login}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Renseignez le login de la caméras}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="login"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label"> {{Mot de passe}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Renseignez le mot de passe}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control inputPassword" data-l1key="configuration" data-l2key="password"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Adresse IP}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Renseignez l'adresse IP de la caméra}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="adresseip"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Port}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Renseignez le port de la caméra}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="port" placeholder="{{80}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{}}</label>
								<div class="col-sm-3">
									<a class="btn btn-default" id="btCheckConnexion"><i class='fa fa-refresh'></i> {{Tester la connexion à la caméra}}</a>
								</div>
							</div>
						</div>

						<!-- Partie droite de l'onglet "Équipement" -->
						<!-- Affiche l'icône du plugin par défaut mais vous pouvez y afficher les informations de votre choix -->
						<div class="col-lg-6">
							<legend><i class="fas fa-info"></i> {{Informations}}</legend>
							<div class="form-group">
								<div class="text-center">
									<img name="icon_visu" src="<?= $plugin->getPathImgIcon(); ?>" style="max-width:160px;"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label"> {{Marque}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="company_name"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Modèle}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="model_number"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label"> {{MAC}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="mac"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label"> {{Firmware}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="fw_ver"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Résolutions}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="resolutions"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label"> {{Nom d'hôte}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="hostname"></span>
								</div>
							</div>
							<legend><i class="fas fa-microchip"></i> {{Capacité hardware}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label"> {{Microphone}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="mic_in"></span>
								</div>
								<label class="col-sm-3 control-label"> {{Hautparleur}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="speaker_out"></span>
								</div>
								<label class="col-sm-3 control-label"> {{Led blanche}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="wlled"></span>
								</div>
								<label class="col-sm-3 control-label"> {{Led infra-rouge}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="irled"></span>
								</div>
								<label class="col-sm-3 control-label"> {{Port série}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="serial"></span>
								</div>
								<label class="col-sm-3 control-label"> {{IO}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="ioctrl"></span>
								</div>
								<label class="col-sm-3 control-label"> {{PT}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="ptctrl"></span>
								</div>
								<label class="col-sm-3 control-label"> {{Bouton de confidentialité}}</label>
								<div class="col-sm-7">
									<span class="eqLogicAttr" data-l1key="configuration" data-l2key="privacy_button"></span>
								</div>
							</div>
						</div>
					</fieldset>
				</form>
				<hr>
			</div><!-- /.tabpanel #eqlogictab-->

			<!-- Onglet des commandes de l'équipement -->
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<a class="btn btn-default btn-sm pull-right cmdAction" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande}}</a>
				<br/><br/>
				<div class="table-responsive">
					<table id="table_cmd" class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th>{{Id}}</th>
								<th>{{Nom}}</th>
								<th>{{Type}}</th>
								<th>{{Paramètres}}</th>
								<th>{{Options}}</th>
								<th>{{Action}}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div><!-- /.tabpanel #commandtab-->
			<!--
			Evènements
			--->
			<div role="tabpanel" class="tab-pane" id="events">
				<div class="col-lg-7" style="padding:10px 35px">
						<legend>
								<span>{{Réglage des déclencheurs}}</span>
						</legend>
						<br />
						<!-- m
								-->
						<div class="container" style="width: 90%;">
								<div class="form-group">
										<!-- table  -->
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
														<tr style="height: 50px !important;">
																<td><label class="control-label">{{Activer les déclencheurs}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="event_trigger" /></td>
														</tr>
													</tbody>
											</table>
											<legend>
													<span>{{Lors de la détection de mouvement}}</span>
											</legend>
											<table class="table table-bordered table-condensed" style="text-align:center">
												<label>{{Action(s) à réaliser :}}</label>
													<tbody>
														<tr style="height: 50px !important;">
																<td><label class="control-label">{{Email}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="email1" /></td>
																<td><label class="control-label">{{HTTP notif}}&ensp;</label><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="httpn1" /></td>
																<td><label class="control-label">{{HTTP post}}&ensp;</label><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="httppost1" /></td>
																<td><label class="control-label">{{IM Jabber}}&ensp;</label><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="im1" /></td>
																<td><label class="control-label">{{LED}}&ensp;</label><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="wlled1" /></td>
																<td><label class="control-label">{{FTP}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="ftpu1" /></td>
																<td><label class="control-label">{{SMBS/CIF}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="smbc_rec1" /></td>
																<td><label class="control-label">{{SDCARD}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="sd_rec1" /></td>
															</tr>
												</tbody>
												</table>
													<table class="table table-bordered table-condensed" style="text-align:center">
														<label>{{Pièce jointe de l'évènement :}}</label>
															<tbody>
																<tr style="height: 50px !important;">
																<td><label class="control-label">{{Interval}}</label></td>
																<td>
																		<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="int1">
																			<?php for ($i = 0; $i <= 10; $i++) {
																					echo "<option value=\"$i\">$i</option>";
																			}?>
																		</select>
																</td>
																<td><label class="control-label">{{Fichier}}</label></td>
																<td>
																		<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="file1">
																				<option value="mp4">MP4</option>
																				<option value="avi">AVI</option>
																				<option value="jpg">JPG</option>
																		</select>
																</td>
																<td><label class="control-label">{{Enregistrement}}</td>
																<td><label class="control-label">{{Durée avant}}</td>
																<td></label><input type="text" class="eqLogicAttr" data-l1key="configuration" data-l2key="startover1" /></td>
																<td><label class="control-label">{{Durée après}}</td>
																<td></label><input type="text" class="eqLogicAttr" data-l1key="configuration" data-l2key="endat1" /></td>
														</tr>
												</tbody>
										</table>
										<legend>
												<span>{{Lors de la détection de bruit}}</span>
										</legend>
										<table class="table table-bordered table-condensed" style="text-align:center">
											<label>{{Action(s) à réaliser :}}</label>
												<tbody>
													<tr style="height: 50px !important;">
															<td><label class="control-label">{{Email}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="email2" /></td>
															<td><label class="control-label">{{HTTP notif}}&ensp;</label><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="httpn2" /></td>
															<td><label class="control-label">{{HTTP post}}&ensp;</label><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="httppost2" /></td>
															<td><label class="control-label">{{IM Jabber}}&ensp;</label><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="im2" /></td>
															<td><label class="control-label">{{LED}}&ensp;</label><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="wlled2" /></td>
															<td><label class="control-label">{{FTP}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="ftpu2" /></td>
															<td><label class="control-label">{{SMBS/CIF}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="smbc_rec2" /></td>
															<td><label class="control-label">{{SDCARD}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="sd_rec2" /></td>
														</tr>
											</tbody>
											</table>
												<table class="table table-bordered table-condensed" style="text-align:center">
													<label>{{Pièce jointe de l'évènement :}}</label>
														<tbody>
															<tr style="height: 50px !important;">
															<td><label class="control-label">{{Interval}}</label></td>
															<td>
																	<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="int2">
																		<?php for ($i = 0; $i <= 10; $i++) {
																				echo "<option value=\"$i\">$i</option>";
																		}?>
																	</select>
															</td>
															<td><label class="control-label">{{Fichier}}</label></td>
															<td>
																	<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="file2">
																		<option value="mp4">MP4</option>
																		<option value="avi">AVI</option>
																		<option value="jpg">JPG</option>
																	</select>
															</td>
															<td><label class="control-label">{{Enregistrement}}</td>
															<td><label class="control-label">{{Durée avant}}</td>
															<td></label><input type="text" class="eqLogicAttr" data-l1key="configuration" data-l2key="startover2" /></td>
															<td><label class="control-label">{{Durée après}}</td>
															<td></label><input type="text" class="eqLogicAttr" data-l1key="configuration" data-l2key="endat2" /></td>
													</tr>
											</tbody>
									</table>
								</div>
						</div>
				</div>
				<div class="col-lg-5" style="padding:15px 35px">
						<legend>
								<span style="text-align:left">{{Action}}</span>
						</legend>

						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btGetMOTION" onclick="GetCAMinfo('EVENT')" class="btn btn-block btn-primary eqLogicAction"><i class="fas fa-download"></i> {{Lire la configuration depuis la caméra}}</a>
								</div>
								<br>
						</div>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btSetMOTION" onclick="SetCAMconfig('EVENT')" class="btn btn-block btn-success eqLogicAction"><i class="fas fa-upload"></i> {{Envoyer la configuration dans la caméra}}</a>
										{{(Cliquez sur "Sauvegarder" avant d'envoyer la config)}}
								</div>
								<br>
						</div>
				</div>
			</div>
			<!--
			HTTP_NOTIFY
			--->
			<div role="tabpanel" class="tab-pane" id="httpnotify">
				<div class="col-lg-7" style="padding:10px 35px">
						<legend>
								<span>{{Réglage des notifications HTTP}}</span>
						</legend>
						<br />
						<!-- http_notify=1
								http_url=
								http_proxy=
								http_proxy_no=80
								http_method=1
								http_user=
								http_password=
								proxy_user=
								proxy_password=
								event_data_flag=1
								-->
						<div class="container" style="width: 90%;">
								<div class="form-group">
										<!-- table  -->
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
														<tr style="height: 50px !important;">
																<td><label class="control-label">{{Activer}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="http_notify" /></td>
														</tr>
														<tr>
																<td><label class="control-label">{{URL d'envoie de la notification (n'accepte pas HTTPS)}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="http_url" placeholder="{{http://my-url-to-notify.com}}"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Methode HTTP}}</label></td>
																<td>
																		<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="http_method">
																				<option value="1">GET</option>
																				<option value="2">POST</option>
																		</select>
																</td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Login (optionnel)}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="http_user"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Mot de passe (optionnel)}}</label></td>
																<td><input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="http_password"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Proxy (optionnel)}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="http_proxy"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Port Proxy (optionnel)}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="http_proxy_no"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Login proxy (optionnel)}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="proxy_user"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Mot de passe proxy (optionnel)}}</label></td>
																<td><input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="proxy_password"/></td>
														</tr>
												</tbody>
										</table>
								</div>
						</div>
				</div>


				<div class="col-lg-5" style="padding:15px 35px">
						<legend>
								<span style="text-align:left">{{Action}}</span>
						</legend>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btGetHTTP_NOTIFY" onclick="GetCAMinfo('HTTP_NOTIFY')" class="btn btn-block btn-primary eqLogicAction"><i class="fas fa-download"></i> {{Lire la configuration depuis la caméra}}</a>
								</div>
								<br>
						</div>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btSetHTTP_NOTIFY" onclick="SetCAMconfig('HTTP_NOTIFY')" class="btn btn-block btn-success eqLogicAction"><i class="fas fa-upload"></i> {{Envoyer la configuration dans la caméra}}</a>
										<span style="text-align:center">{{(Cliquez sur "Sauvegarder" avant d'envoyer la config)}}</span>
								</div>
								<br>
						</div>
				</div>
			</div>
<!--
MAIL Notif
--->
			<div role="tabpanel" class="tab-pane" id="mailnotif">
				<div class="col-lg-7" style="padding:10px 35px">
						<legend>
								<span>{{Réglage des notifications email}}</span>
						</legend>
						<br />
						<!-- smtp_enable=0
								smtp_server=
								pop_server=
								smtp_port=25
								smtp_auth=0
								smtp_account=
								smtp_password=
								smtp2_enable=0
								smtp2_server=
								pop2_server=
								smtp2_port=25
								smtp2_auth=0
								smtp2_account=
								smtp2_password=
								from_addr=
								from_addr2=
								to_addr1=
								to_addr2=
								to_addr3=
								send_email=
								email_att=7
								subject=
								-->
						<div class="container" style="width: 90%;">
								<div class="form-group">
										<!-- table  -->
										<legend>
												<span>{{Serveur SMTP primaire}}</span>
										</legend>
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
														<tr style="height: 50px !important;">
																<td><label class="control-label">{{Activer}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="smtp_enable" /></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Serveur SMTP}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp_server" placeholder="{{smtp.free.fr}}"/></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Port SMTP}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp_port" placeholder="{{25}}"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Serveur POP}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="pop_server" placeholder="{{pop.free.fr}}"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Login SMTP}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp_account"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Mot de passe SMTP}}</label></td>
																<td><input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp_password"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Authentification}}</label></td>
																<td>
																		<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp_auth">
																				<option value="0">Aucune</option>
																				<option value="1">SMTP</option>
																				<option value="2">POP avant SMTP</option>
																		</select>
																</td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Adresse d'envoie}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="from_addr"/></td>
														</tr>
													</tbody>
											</table>
											<!-- table  -->
											<legend>
													<span>{{Serveur SMTP secondaire}}</span>
											</legend>
											<label class="control-label">{{(Optionnel : Le SMTP secondaire est utilisé en backup uniquement lorsque le SMTP primaire n'est pas accessible.)}}</label>
											<table class="table table-bordered table-condensed" style="text-align:center">
													<tbody>
															<tr style="height: 50px !important;">
																	<td><label class="control-label">{{Activer}}</label></td>
																	<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="smtp2_enable" /></td>
															</tr>
															<tr>
																	<td><label class="control-label">{{Serveur SMTP}}</label></td>
																	<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp2_server" placeholder="{{smtp.free.fr}}"/></td>
															</tr>
															<tr>
																	<td><label class="control-label">{{Port SMTP}}</label></td>
																	<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp2_port" placeholder="{{25}}"/></td>
															</tr>
															<tr>
																	<td><label class="control-label ">{{Serveur POP}}</label></td>
																	<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="pop2_server" placeholder="{{pop.free.fr}}"/></td>
															</tr>
															<tr>
																	<td><label class="control-label ">{{Login SMTP}}</label></td>
																	<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp2_account"/></td>
															</tr>
															<tr>
																	<td><label class="control-label ">{{Mot de passe SMTP}}</label></td>
																	<td><input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp2_password"/></td>
															</tr>
															<tr>
																	<td><label class="control-label ">{{Authentification}}</label></td>
																	<td>
																			<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="smtp2_auth">
																					<option value="0">Aucune</option>
																					<option value="1">SMTP</option>
																					<option value="2">POP avant SMTP</option>
																			</select>
																	</td>
															</tr>
															<tr>
																	<td><label class="control-label ">{{Adresse d'envoie}}</label></td>
																	<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="from_addr2"/></td>
															</tr>
														</tbody>
												</table>
											<!-- table  -->
											<legend>
													<span>{{Paramètre email}}</span>
											</legend>
											<table class="table table-bordered table-condensed" style="text-align:center">
													<tbody>
														<tr>
																<td><label class="control-label ">{{Sujet}}</label></td>
																<td></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="subject" placeholder="{{Nouvel évènement}}"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Destinataire n°1}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="addmail1" /></td>
																<td><label class="control-label ">{{Ajouter PJ}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="mailaddattachement1" /></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="to_addr1"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Destinataire n°2}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="addmail2" /></td>
																<td><label class="control-label ">{{Ajouter PJ}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="mailaddattachement2" /></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="to_addr2"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Destinataire n°3}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="addmail3" /></td>
																<td><label class="control-label ">{{Ajouter PJ}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="mailaddattachement3" /></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="to_addr3"/></td>
														</tr>
												</tbody>
										</table>
								</div>
						</div>
				</div>

				<div class="col-lg-5" style="padding:15px 35px">
						<legend>
								<span style="text-align:left">{{Action}}</span>
						</legend>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btGetEMAIL" onclick="GetCAMinfo('EMAIL')" class="btn btn-block btn-primary eqLogicAction"><i class="fas fa-download"></i> {{Lire la configuration depuis la caméra}}</a>
								</div>
								<br>
						</div>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btSetEMAIL" onclick="SetCAMconfig('EMAIL')" class="btn btn-block btn-success eqLogicAction"><i class="fas fa-upload"></i> {{Envoyer la configuration dans la caméra}}</a>
										(Cliquez sur "Sauvegarder" avant d'envoyer la config)
								</div>
								<br>
						</div>
				</div>
			</div>
			<!--
			FTP Send
			--->
			<div role="tabpanel" class="tab-pane" id="ftpsend">
				<div class="col-lg-7" style="padding:10px 35px">
						<legend>
								<span>{{Réglage du FTP}}</span>
						</legend>
						<br />
						<!-- ftp1=0
								ftp1_server=
								ftp1_account=
								ftp1_passwd=
								ftp1_path=
								ftp1_passive=
								ftp1_port=21
								ftp2=0
								ftp2_server=
								ftp2_account=
								ftp2_passwd=
								ftp2_path=
								ftp2_passive=
								ftp2_port=21
								-->
						<div class="container" style="width: 90%;">
								<div class="form-group">
									<legend>
											<span>{{Serveur FTP n°1}}</span>
									</legend>
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
														<tr style="height: 50px !important;">
																<td><label class="control-label">{{Activer FTP}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="ftp1" /></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Serveur}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp1_server"/></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Login}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp1_account"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Mot de passe}}</label></td>
																<td><input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp1_passwd"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Chemin}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp1_path"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Port}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp1_port" placeholder="{{21}}"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Mode passif}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="ftp1_passive" /></td>
														</tr>
													</tr>
											</tbody>
									</table>

									<legend>
											<span>{{Serveur FTP n°2}}</span>
									</legend>
									<label class="control-label">{{(Optionnel : le FTP n°2 est utilisé en backup uniquement lorsque le FTP n°1 n'est pas accessible.)}}</label>
									<table class="table table-bordered table-condensed" style="text-align:center">
											<tbody>
														<tr style="height: 50px !important;">
																<td><label class="control-label">{{Activer FTP}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="ftp2" /></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Serveur}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp2_server"/></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Login}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp2_account"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Mot de passe}}</label></td>
																<td><input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp2_passwd"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Chemin}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp2_path"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Port}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ftp2_port" placeholder="{{21}}"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Mode passif}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="ftp2_passive" /></td>
														</tr>
												</tbody>
										</table>
								</div>
						</div>
				</div>

				<div class="col-lg-5" style="padding:15px 35px">
						<legend>
								<span style="text-align:left">{{Action}}</span>
						</legend>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btGetFTP" onclick="GetCAMinfo('FTP')" class="btn btn-block btn-primary eqLogicAction"><i class="fas fa-download"></i> {{Lire la configuration depuis la caméra}}</a>
								</div>
								<br>
						</div>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btSetFTP" onclick="SetCAMconfig('FTP')" class="btn btn-block btn-success eqLogicAction"><i class="fas fa-upload"></i> {{Envoyer la configuration dans la caméra}}</a>
										(Cliquez sur "Sauvegarder" avant d'envoyer la config)
								</div>
								<br>
						</div>
				</div>
			</div>
			<!--
			Mouvement
			--->
			<div role="tabpanel" class="tab-pane" id="motion">
				<div class="col-lg-7" style="padding:10px 35px">
						<legend>
								<span>{{Réglage de la détection de mouvement}}</span>
						</legend>
						<br />
						<!-- md_mode=0
									md_switch1=1
									md_switch2=0
									md_switch3=0
									md_switch4=0
									md_name1=Window 1
									md_name2=Window 2
									md_name3=Window 3
									md_name4=Window 4
									md_window1=0,0,639,479
									md_window2=0,0,160,120
									md_window3=0,0,160,120
									md_window4=0,0,160,120
									md_abs_window1=0,0,1280,720/1280,720
									md_abs_window2=0,0,320,180/1280,720
									md_abs_window3=0,0,320,180/1280,720
									md_abs_window4=0,0,320,180/1280,720
									md_threshold1=40
									md_threshold2=127
									md_threshold3=127
									md_threshold4=127
									md_sensitivity1=7
									md_sensitivity2=6
									md_sensitivity3=6
									md_sensitivity4=6
									md_update_freq1=90
									md_update_freq2=90
									md_update_freq3=90
									md_update_freq4=90
								-->
						<div class="container" style="width: 90%;">
								<div class="form-group">
										<!-- table  -->
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
														<tr style="height: 50px !important;">
																<td><label class="control-label">{{Activer la déctection de mouvement}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="md_mode" /></td>
														</tr>
													</tbody>
											</table>
											<legend>
													<span>{{Fenetre de détection n°1}}</span>
											</legend>
											<table class="table table-bordered table-condensed" style="text-align:center">
													<tbody>
														<tr>
																<td><label class="control-label">{{Activer}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="md_switch1" /></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_name1"/></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Coordonnées fenêtre de détection}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_window1"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Valeur ABS fenêtre}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_abs_window1"/></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Sensibilité}}</label></td>
																<td><input type="range" min="0" max="10" step="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_sensitivity1"></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Seuil}}</label></td>
																<td><input type="range" min="0" max="255" step="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_threshold1"></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Fréquence de détection}}</label></td>
																<td>
																		<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_update_freq1">
																				<option value="90">90</option>
																		</select>
																</td>
														</tr>
												</tbody>
										</table>
										<legend>
												<span>{{Fenetre de détection n°2}}</span>
										</legend>
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
													<tr>
															<td><label class="control-label">{{Activer}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="md_switch2" /></td>
															<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_name2"/></td>
													</tr>
													<tr>
															<td><label class="control-label">{{Coordonnées fenêtre de détection}}</label></td>
															<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_window2"/></td>
													</tr>
													<tr>
															<td><label class="control-label ">{{Valeur ABS fenêtre}}</label></td>
															<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_abs_window2"/></td>
													</tr>
													<tr>
															<td><label class="control-label ">{{Sensibilité}}</label></td>
															<td><input type="range" min="0" max="10" step="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_sensitivity2"></td>
													</tr>
													<tr>
															<td><label class="control-label ">{{Seuil}}</label></td>
															<td><input type="range" min="0" max="255" step="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_threshold2"></td>
													</tr>
													<tr>
															<td><label class="control-label ">{{Fréquence de détection}}</label></td>
															<td>
																	<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_update_freq2">
																			<option value="90">90</option>
																	</select>
															</td>
													</tr>
											</tbody>
									</table>
									<legend>
											<span>{{Fenetre de détection n°3}}</span>
									</legend>
									<table class="table table-bordered table-condensed" style="text-align:center">
											<tbody>
												<tr>
														<td><label class="control-label">{{Activer}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="md_switch3" /></td>
														<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_name3"/></td>
												</tr>
												<tr>
														<td><label class="control-label">{{Coordonnées fenêtre de détection}}</label></td>
														<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_window3"/></td>
												</tr>
												<tr>
														<td><label class="control-label ">{{Valeur ABS fenêtre}}</label></td>
														<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_abs_window3"/></td>
												</tr>
												<tr>
														<td><label class="control-label ">{{Sensibilité}}</label></td>
														<td><input type="range" min="0" max="10" step="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_sensitivity3"></td>
												</tr>
												<tr>
														<td><label class="control-label ">{{Seuil}}</label></td>
														<td><input type="range" min="0" max="255" step="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_threshold3"></td>
												</tr>
												<tr>
														<td><label class="control-label ">{{Fréquence de détection}}</label></td>
														<td>
																<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_update_freq3">
																		<option value="90">90</option>
																</select>
														</td>
												</tr>
										</tbody>
								</table>
								<legend>
										<span>{{Fenetre de détection n°4}}</span>
								</legend>
								<table class="table table-bordered table-condensed" style="text-align:center">
										<tbody>
											<tr>
													<td><label class="control-label">{{Activer}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="md_switch4" /></td>
													<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_name4"/></td>
											</tr>
											<tr>
													<td><label class="control-label">{{Coordonnées fenêtre de détection}}</label></td>
													<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_window4"/></td>
											</tr>
											<tr>
													<td><label class="control-label ">{{Valeur ABS fenêtre}}</label></td>
													<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_abs_window4"/></td>
											</tr>
											<tr>
													<td><label class="control-label ">{{Sensibilité}}</label></td>
													<td><input type="range" min="0" max="10" step="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_sensitivity4"></td>
											</tr>
											<tr>
													<td><label class="control-label ">{{Seuil}}</label></td>
													<td><input type="range" min="0" max="255" step="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_threshold4"></td>
											</tr>
											<tr>
													<td><label class="control-label ">{{Fréquence de détection}}</label></td>
													<td>
															<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="md_update_freq4">
																	<option value="90">90</option>
															</select>
													</td>
											</tr>
									</tbody>
							</table>
								</div>
						</div>
				</div>

				<div class="col-lg-5" style="padding:15px 35px">
						<legend>
								<span style="text-align:left">{{Action}}</span>
						</legend>

						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btGetMOTION" onclick="GetCAMinfo('MOTION')" class="btn btn-block btn-primary eqLogicAction"><i class="fas fa-download"></i> {{Lire la configuration depuis la caméra}}</a>
								</div>
								<br>
						</div>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btSetMOTION" onclick="SetCAMconfig('MOTION')" class="btn btn-block btn-success eqLogicAction"><i class="fas fa-upload"></i> {{Envoyer la configuration dans la caméra}}</a>
										(Cliquez sur "Sauvegarder" avant d'envoyer la config)
								</div>
								<br>
						</div>
				</div>
			</div>
			<!--
			Audio
			--->
			<div role="tabpanel" class="tab-pane" id="audio">
				<div class="col-lg-7" style="padding:10px 35px">
						<legend>
								<span>{{Réglage de la détection de bruit}}</span>
						</legend>
						<br />
						<!-- audio_in=1
								in_volume=80
								in_audio_type=5,3
								audio_mode=1
								operation_mode=1
								au_trigger_en=0
								au_trigger_volume=17
								au_trigger_method=0
								in_pcm_sr=16000,5512
								audio_advanced_mode=0
								in_volume_again=23
								audio_out=0
								out_volume=34
								-->
						<div class="container" style="width: 90%;">
								<div class="form-group">
										<!-- table  -->
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
														<tr style="height: 50px !important;">
																<td><label class="control-label">{{Activer microphone}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="audio_in" /></td>
														</tr>
														<tr style="height: 50px !important;">
																<td><label class="control-label">{{Déctection de bruit}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="au_trigger_en" /></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Volume de détection}}</label></td>
																<td><input type="range" min="0" max="100" step="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="in_volume"></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Sensibilité de détection}}</label></td>
																<td><input type="range" min="2" max="50" step="2" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="au_trigger_volume"></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Déclencheur audio}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="au_trigger_method">
																			<option value="0">Lorsqu'il y a un bruit</option>
																			<option value="1">Lorsqu'il n'y a plus de bruit</option>
																	</select></td>
														</tr>
												</tbody>
										</table>
								</div>
						</div>
				</div>

				<div class="col-lg-5" style="padding:15px 35px">
						<legend>
								<span style="text-align:left">{{Action}}</span>
						</legend>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btGetAUDIO" onclick="GetCAMinfo('AUDIO')" class="btn btn-block btn-primary eqLogicAction"><i class="fas fa-download"></i> {{Lire la configuration depuis la caméra}}</a>
								</div>
								<br>
						</div>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btSetAUDIO" onclick="SetCAMconfig('AUDIO')" class="btn btn-block btn-success eqLogicAction"><i class="fas fa-upload"></i> {{Envoyer la configuration dans la caméra}}</a>
										(Cliquez sur "Sauvegarder" avant d'envoyer la config)
								</div>
								<br>
						</div>

				</div>
			</div>
			<!--
			Audio
			--->
			<div role="tabpanel" class="tab-pane" id="video">
				<div class="col-lg-7" style="padding:10px 35px">
						<legend>
								<span>{{Réglage vidéo}}</span>
						</legend>
						<br />
						<!-- video_schedule=0
								video_define1=
								video_define2=
								video_define3=
								video_define4=
								video_define5=
								video_define6=
								video_define7=
								video_define8=
								video_define9=
								video_define10=
								time_stamp=0
								text_overlay=0
								text=
								power_line=50
								color=0
								exposure=4
								sharpness=4
								flip=0
								mirror=0
								hue=4
								saturation=4
								contrast=4
								dn_filter=0
								dn_sch=0
								dn_sch_hr=0
								dn_sch_min=0
								dn_hrend=0
								dn_minend=0
								night_mode=0
								dn_interval=3
								default_channel=1
								dn_threshold=35,75
								aspect_ratio=0
								-->
						<div class="container" style="width: 90%;">
								<div class="form-group">
										<!-- table  -->
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
														<tr>
																<td><label class="control-label">{{Afficher horodatage}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="time_stamp" /></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Afficher texte}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="text_overlay" /></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="text"/></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Retourner image}}</label></td>
																<td><label class="control-label">{{Verticalement}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="flip" /><label class="control-label">{{Horizontalement}}</label>&ensp;<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="mirror" /></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Balance des blancs}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="color">
																			<option value="0">Auto</option>
																			<option value="1">Intérieur</option>
																			<option value="2">Eclairage Blanc</option>
																			<option value="3">Eclairage Jaune</option>
																			<option value="4">Extérieur</option>
																			<option value="5">Noir & Blanc</option>
																	</select></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Exposition}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="exposure">
																	<?php for ($i = 1; $i <= 7; $i++) {
																			echo "<option value=\"$i\">$i</option>";
																	}?>
																	</select></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Netteté}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="sharpness">
																	<?php for ($i = 1; $i <= 7; $i++) {
																			echo "<option value=\"$i\">$i</option>";
																	}?>
																	</select></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Hue}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="hue">
																	<?php for ($i = 1; $i <= 7; $i++) {
																			echo "<option value=\"$i\">$i</option>";
																	}?>
																	</select></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Saturation}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="saturation">
																	<?php for ($i = 1; $i <= 7; $i++) {
																			echo "<option value=\"$i\">$i</option>";
																	}?>
																	</select></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Contraste}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="contrast">
																	<?php for ($i = 1; $i <= 7; $i++) {
																			echo "<option value=\"$i\">$i</option>";
																	}?>
																	</select></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Ratio de l'image}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="aspect_ratio">
																	<?php for ($i = 0; $i <= 2; $i++) {
																			echo "<option value=\"$i\">$i</option>";
																	}?>
																	</select></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Vision nocturne (Led IR)}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="dn_sch">
																			<option value="0">Auto</option>
																			<!--<option value="1">Planning</option>-->
																			<option value="2">Désactivé</option>
																			<option value="3">Toujours activé</option>
																	</select></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Délai changement nuit/jour}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="dn_interval">
																	<?php for ($i = 0; $i <= 10; $i++) {
																			echo "<option value=\"$i\">$i</option>";
																	}?>
																	</select></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Seuil Jour/Nuit}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="dn_threshold"/></td>
														</tr>
												</tbody>
										</table>
								</div>
						</div>
				</div>

				<div class="col-lg-5" style="padding:15px 35px">
						<legend>
								<span style="text-align:left">{{Action}}</span>
						</legend>

						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btGetVIDEO" onclick="GetCAMinfo('VIDEO')" class="btn btn-block btn-primary eqLogicAction"><i class="fas fa-download"></i> {{Lire la configuration depuis la caméra}}</a>
								</div>
								<br>
						</div>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btSetVIDEO" onclick="SetCAMconfig('VIDEO')" class="btn btn-block btn-success eqLogicAction"><i class="fas fa-upload"></i> {{Envoyer la configuration dans la caméra}}</a>
										(Cliquez sur "Sauvegarder" avant d'envoyer la config)
								</div>
								<br>
						</div>
				</div>
			</div>
			<!--
			JPEG
			--->
			<div role="tabpanel" class="tab-pane" id="jpeg">
				<div class="col-lg-7" style="padding:10px 35px">
						<legend>
								<span>{{Réglage JPEG}}</span>
						</legend>
						<br />
						<!-- mode=0
								resolution=4
								quality_level=3
								frame_rate=15
								sp_uri=
								mode2=0
								resolution2=3
								quality_level2=3
								frame_rate2=15
								sp_uri2=
								mode3=0
								resolution3=4
								quality_level3=3
								frame_rate3=15
								sp_uri3=
								bandwidth=0
								cropping=0
								bandwidth2=0
								cropping2=0
								bandwidth3=0
								cropping3=0
								-->
						<div class="container" style="width: 90%;">
								<div class="form-group">
										<!-- table  -->
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
														<tr>
																<td><label class="control-label">{{Mode 1}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="mode" /></td>
																<td><span class="eqLogicAttr" data-l1key="configuration" data-l2key="mode_state"></span></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Résolution}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="resolution">
																			<option value="1">160x120</option>
																			<option value="2">320x240</option>
																			<option value="3">640x480</option>
																			<option value="4">1280x720</option>
																	</select></td>
																<td><span class="eqLogicAttr" data-l1key="configuration" data-l2key="resolution_state"></span></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Qualité}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="quality_level">
																			<option value="1">Minimale</option>
																			<option value="2">Basse</option>
																			<option value="3">Normal</option>
																			<option value="4">Haute</option>
																			<option value="5">Maximale</option>
																	</select></td>
																<td><span class="eqLogicAttr" data-l1key="configuration" data-l2key="quality_level_state"></span></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Fréquence d'images (1-30)}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="frame_rate"/></td>
																<td><span class="eqLogicAttr" data-l1key="configuration" data-l2key="frame_rate_state"></span></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Bande passante}}</label></td>
																<td><select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="bandwidth">
																			<option value="0">Auto</option>
																			<option value="1">64K</option>
																			<option value="2">128K</option>
																			<option value="3">256K</option>
																			<option value="4">512K</option>
																			<option value="5">768K</option>
																			<option value="6">1024K</option>
																			<option value="7">1.5M</option>
																			<option value="8">2M</option>
																	</select></td>
																<td><span class="eqLogicAttr" data-l1key="configuration" data-l2key="bandwidth_state"></span></td>
														</tr>
												</tbody>
										</table>
								</div>
						</div>
				</div>

				<div class="col-lg-5" style="padding:15px 35px">
						<legend>
								<span style="text-align:left">{{Action}}</span>
						</legend>

						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btGetJPEG" onclick="GetCAMinfo('JPEG')" class="btn btn-block btn-primary eqLogicAction"><i class="fas fa-download"></i> {{Lire la configuration depuis la caméra}}</a>
								</div>
								<br>
						</div>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btSetJPEG" onclick="SetCAMconfig('JPEG')" class="btn btn-block btn-success eqLogicAction"><i class="fas fa-upload"></i> {{Envoyer la configuration dans la caméra}}</a>
										(Cliquez sur "Sauvegarder" avant d'envoyer la config)
								</div>
								<br>
						</div>
				</div>
			</div>
			<!--
			Network
			--->
			<div role="tabpanel" class="tab-pane" id="network">
				<div class="col-lg-7" style="padding:10px 35px">
						<legend>
								<span>{{Réglage réseau}}</span>
						</legend>
						<br />
						<!--
								-->
						<div class="container" style="width: 90%;">
								<div class="form-group">
									<legend>
											<span>{{Réseau}}</span>
									</legend>
										<table class="table table-bordered table-condensed" style="text-align:center">
												<tbody>
														<tr>
																<td><label class="control-label">{{Activer QOS}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="qos_enable" /></td>
																<td><label class="control-label">{{DSCP (entre 0-63)}}</label></td>
																<td><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="qos_dscp" placeholder="{{32}}"/></td>
														</tr>
														<tr>
																<td><label class="control-label">{{Service Bonjour}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="bonjour_mode" /></td>
																<td><label class="control-label">{{Nom}}</label></td>
																<td><input type="text" class="eqLogicAttr" data-l1key="configuration" data-l2key="bonjour_name" /></td>
														</tr>
														<tr>
																<td><label class="control-label ">{{Activer la découverte UPnP}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="upnp_mode" /></td>
																<td><label class="control-label ">{{Mappage UPnP}}</label></td>
																<td><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="upnp_traversal" /></td></tr>
													</tr>
											</tbody>
									</table>
								</div>
						</div>
				</div>

				<div class="col-lg-5" style="padding:15px 35px">
						<legend>
								<span style="text-align:left">{{Action}}</span>
						</legend>
						<div class="container-fluid">
								<div class="form-group">
										<a class="btn btn-block btn-default eqLogicAction" id="btGetConfigNETWORK"><i class="fas fa-download"></i> {{Lire la configuration depuis la caméra}}</a>
								</div>
								<br>
						</div>
						<div class="container-fluid">
								<div class="form-group">
										<a href="javascript:void(0);" id="btSetNETWORK" onclick="SetCAMconfig('NETWORK')" class="btn btn-block btn-success eqLogicAction"><i class="fas fa-upload"></i> {{Envoyer la configuration dans la caméra}}</a>
										(Cliquez sur "Sauvegarder" avant d'envoyer la config)
								</div>
								<br>
						</div>

				</div>
			</div>
		</div><!-- /.tab-content -->
	</div><!-- /.eqLogic -->
</div><!-- /.row row-overflow -->

<script>
    $('#btCheckConnexion').on('click', function () {
        $.ajax({// fonction permettant de faire de l'ajax
            type: "POST", // methode de transmission des données au fichier php
            url: "plugins/sercomm/core/ajax/sercomm.ajax.php", // url du fichier php
            data: {
            	action: "CheckConnexion",
							id : $('.eqLogicAttr[data-l1key=id]').value()
            },
            dataType: 'json',
            error: function (request, status, error) {
            	handleAjaxError(request, status, error);
            },
            success: function (data) { // si l'appel a bien fonctionné
            if (data.state != 'ok') {
            	$('#div_alert').showAlert({message: data.result, level: 'danger'});
            	return;
            }
            $('#div_alert').showAlert({message: '{{Connexion à la caméra réussie}}', level: 'success'});
          }
        });
    });
		/* Actions des boutons sur la page */
		function SetCAMconfig(REQgroup){
			$.ajax({// fonction permettant de faire de l'ajax
					type: "POST", // methode de transmission des données au fichier php
					url: "plugins/sercomm/core/ajax/sercomm.ajax.php", // url du fichier php
					data: {
						action: "SetCAMConfig",
						group:REQgroup,
						id : $('.eqLogicAttr[data-l1key=id]').value()
					},
					dataType: 'json',
					error: function (request, status, error) {
						handleAjaxError(request, status, error);
					},
					success: function (data) { // si l'appel a bien fonctionné
					if (data.state != 'ok') {
						$('#div_alert').showAlert({message: data.result, level: 'danger'});
						return;
					}
					$('#div_alert').showAlert({message: '{{OK configuration envoyé avec succès}}', level: 'success'});
					window.location.reload();
				}
			});
		}
		/* Actions des boutons sur la page */
		function GetCAMinfo(REQgroup){
			$.ajax({// fonction permettant de faire de l'ajax
					type: "POST", // methode de transmission des données au fichier php
					url: "plugins/sercomm/core/ajax/sercomm.ajax.php", // url du fichier php
					data: {
						action: "GetCAMConfig",
						group:REQgroup,
						id : $('.eqLogicAttr[data-l1key=id]').value()
					},
					dataType: 'json',
					error: function (request, status, error) {
						handleAjaxError(request, status, error);
					},
					success: function (data) { // si l'appel a bien fonctionné
					if (data.state != 'ok') {
						$('#div_alert').showAlert({message: data.result, level: 'danger'});
						return;
					}
					$('#div_alert').showAlert({message: '{{Lectue OK}}', level: 'success'});
					window.location.reload();
				}
			});
		}
</script>

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, id_du_plugin) -->
<?php include_file('desktop', 'sercomm', 'js', 'sercomm');?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js');?>
