$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$('#table_cmd tbody').delegate('.bt_selectCmdExpression', 'click', function() {
	var _this=this;
	$(this).value()
	jeedom.cmd.getSelectModal({cmd: {type: 'info'},eqLogic: {eqType_name : ''}}, function (result) {
		$(_this).closest('.cmd').find('.eqLogicAttr[data-l1key=configuration][data-l2key=snapshots]').val(result.human);
	});
});   
//function SelectCamera() {
		$.ajax({
		type: "POST",
		timeout:8000, 
		url: "plugins/facedetection/core/ajax/facedetection.ajax.php",
		data: {
			action: "SearchCamera",
		},
		dataType: 'json',
		error: function(request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function(data) { 
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			if (data.result!=false)
			{
			var Valeur=$('.eqLogicAttr[data-l1key=configuration][data-l2key=snapshots]').val();
			var Select=$('<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="snapshots">');
				for (var i in data.result) {
					Select.append(
						$('<option>').attr('value',data.result[i].Id)
						.text(data.result[i].Nom));
				}
			$('.CameraSelect').html(Select);
			$('.eqLogicAttr[data-l1key=configuration][data-l2key=snapshots] option[value="'+Valeur+'"]').prop('selected', true);
			}
		}
	});	
//}
function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
        var _cmd = {};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
	var tr =$('<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">');
  	tr.append($('<td>')
		.append($('<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove">'))
		.append($('<i class="fa fa-arrows-v pull-left cursor bt_sortable" style="margin-top: 9px;">')));
	tr.append($('<td>')
		.append($('<div>')
			.append($('<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">'))
			.append($('<input class="cmdAttr form-control input-sm" data-l1key="name" value="' + init(_cmd.name) + '" placeholder="{{Name}}" title="Name">')))
		.append($('<div>')
			.append($('<a class="cmdAction btn btn-default btn-sm" data-l1key="chooseIcon">')
				.append($('<i class="fa fa-flag">')).text('Icone'))
			.append($('<span class="cmdAttr" data-l1key="display" data-l2key="icon" style="margin-left : 10px;">'))));
	tr.append($('<td>')
			.append($('<div style="width : 40%;display : inline-block;">')
			.append($('<span>')
				.append($('<input type="checkbox" class="cmdAttr bootstrapSwitch" data-size="mini" data-label-text="{{Historiser}}" data-l1key="isHistorized" checked/>')))
			.append($('</br>'))
			.append($('<span>')
				.append($('<input type="checkbox" class="cmdAttr bootstrapSwitch" data-size="mini" data-label-text="{{Afficher}}" data-l1key="isVisible" checked/>'))))
			.append($('<div style="width : 40%;display : inline-block;">')
				.append($('<input type="hidden" class="cmdAttr form-control input-sm" data-l1key="cache" data-l2key="enable" />'))
				.append($('<label>').text('{{Lifetime}}')
					.append($('<input type="texte" class="cmdAttr form-control input-sm" data-l1key="cache" data-l2key="lifetime" placeholder="{{Lifetime}}" title="Lifetime"/>')))
				.append($('<input type="hidden" class="cmdAttr" data-l1key="type" value="info" />'))
				.append($('<input type="hidden" class="cmdAttr" data-l1key="subType" value="binary" />'))));  
  
		var parmetre=$('<td>');
	if (is_numeric(_cmd.id)) {
		parmetre.append($('<a class="btn btn-default btn-xs cmdAction" data-action="test">')
			.append($('<i class="fa fa-rss">')
				.text('{{Tester}}')));
	}
	parmetre.append($('<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure">')
		.append($('<i class="fa fa-cogs">')))
	.append($('<a class="btn btn-default btn-xs cmdAction expertModeVisible tooltips" data-action="copy" title="{{Dupliquer}}">')
		.append($('<i class="fa fa-files-o">')));
	tr.append(parmetre);
	$('#table_cmd tbody').append(tr);
	$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
	}