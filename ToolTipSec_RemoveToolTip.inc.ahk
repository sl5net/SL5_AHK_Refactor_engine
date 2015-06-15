#Include init_global.init.inc.ahk
RemoveToolTip()
{
	gosub,RemoveToolTip
	}
	;~ l;~ ll
	RemoveToolTip:
	Last_A_This:=A_ThisFunc . A_ThisLabel
	lll(A_LineNumber, "ToolTipSec_RemoveToolTip.inc.ahk",Last_A_This)
	ToolTip,
	SetTimer, RemoveToolTip, Off
	return
	#Include UPDATEDSCRIPT_global.inc.ahk
	