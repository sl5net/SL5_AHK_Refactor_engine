#Include init_global.init.inc.ahk

;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
lll(ln, scriptName, text="")
{	
	global GLOBAL_lllog_only_this_scriptName
	scriptName := trim(scriptName)
	GLOBAL_lllog_only_this_scriptName := trim(GLOBAL_lllog_only_this_scriptName)
	if(StrLen(GLOBAL_lllog_only_this_scriptName)>0) {
	do_createLog_notAppendLog:=true
		if(scriptName != GLOBAL_lllog_only_this_scriptName)
			return false
	}
	

;~ logFileName=log\%A_ScriptName%.log.txt
	logFileName=log\%scriptName%.log.txt
	
					; M = Modification time (this is the default if the parameter is omitted)
		FileGetTime, cFileMTime, %logFileName%, M
	
		diff_cFileMTime_Now_hour:=A_Now
		EnvSub, diff_cFileMTime_Now_hour, %cFileMTime%, hours
		
		diff_cFileMTime_Now_min:= Round(diff_cFileMTime_Now_hour / 60)

		diff_cFileMTime_Now_day:=A_Now
		EnvSub, diff_cFileMTime_Now_day, %cFileMTime%, days
		
		diff_cFileMTime_Now_year:= Round(diff_cFileMTime_Now_day / 365)
		;~ EnvSub, diff_cFileMTime_Now_year, %cFileMTime%, year

		if(diff_cFileMTime_Now_hour > 1)
			FileDelete,%logFileName%

		;~ if(diff_cFileMTime_Now_day > 7)
			;~ FileDelete,%logFileName%
	
	
	
	if(StrLen(scriptName) < 5 ) ; || "functions_global.inc.ahk" != A_ScriptName ... for that we need a PreCompiler !!!
	{
		lll(A_LineNumber, "functions_global.inc.ahk")
	
		;~ t := ""
		;~ t .= "#Include init_global.init.inc.ahk" . "`n"
		;~ t .= "#Include functions_global.inc.ahk" . "`n"
		;~ Clipboard := t
	
		Clipboard="%A_ScriptName%" 
		MsgBox, functions_global.inc.ahk `n ln=%ln% `n  scriptName = %scriptName% `n parameter FILE must not be empty `n `n you find this now inside your clipboard : %Clipboard% `n `n move to line %ln% and fix the bug. `n `n or let run the SL5_AHK_preparser.ahk
		return -1		
	}
	;~ tipp: use notepadd++ , diverses> ohne rückfraen aktuallisieren
	;~ tipp: use notepadd++ , diverses> nach aktuallisierung zum ende springen
	msg:=""
	;~ msg.= ";<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<`n"
	
	if(strlen(text)>0)
		msgtext := """" . text . """"
	else
		msgtext := text
	msg.= scriptName . ">" . ln  . msgtext  . "`n"
	;~ msg.= ";>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>`n`n"
	
	global lll
	if(StrLen(lll)>0)
		lll .= msg
	else	
		lll := msg
	Suspend,on
	if(!FileExist("log"))
	{
		FileCreateDir,log
		if(true != InStr(FileExist("log"), "D") )
		{
		;~ would be true only if the file exists and is a directory
		MsgBox,15-05-15_17-00 ops Who could we store logfiles ?
		}
	}
	
;~if(StrLen(GLOBAL_lllog_only_this_scriptName)>0
	if(do_createLog_notAppendLog)
	{
		FileDelete,%logFileName%
		while(FileExist(logFileName))
			Sleep,100
		gLOBAL_lllog = GLOBAL_lllog_only_this_scriptName
		strLen_GLOBAL_lllog := StrLen(gLOBAL_lllog)
		subStr_lll__strLen := SubStr(lll,1,strLen_GLOBAL_lllog)
		if(subStr_lll__strLen != gLOBAL_lllog)
		{
		;~ MsgBox,%subStr_lll__strLen% %GLOBAL_lllog_only_this_scriptName% := GLOBAL_lllog_only_this_scriptName `n
		lll := "GLOBAL_lllog_only_this_scriptName = " . GLOBAL_lllog_only_this_scriptName . "`n" . lll 
		}
	}
	
	FileAppend, % lll, %logFileName%
	;~ ToolTip,%logFileName% := logFileName `n
	;~ MsgBox,%lll%
	Suspend,off
	return
}
;>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

runCopyQ_Ctrl_Shift_v(){
	MsgBox,Ctrl Shift v `n  dont work actually. `n please use Ctrl Shift 1. `n Sorry about that. thanks. 15.06.2015
	return
	; „<LEER> - CopyQ ahk_cl!A_ScriptDir!A_ScriptDir!A_ScriptDiraA_ScriptDirA_ScriptDirss QWidget...“ (3 Zeilen) - CopyQ ahk_class1`11`n`n`n
		SetTitleMatchMode,2
		DetectHiddenWindows,on
	IfWinNotExist,CopyQ ahk_class QWidget
	{
		MsgBox,it not exist
		run,%A_ScriptDir%\SL5_AHK_Refactor_engine\copyq-windows\copyq.exe
		Sleep,2000
	}
  ; ^+v!; ^+v!; ^+v!; ^+v!; ^+v!; ^+v!; ^+v!; ^+vCopyQ ahk_class QWidget
    ; ^+v7!; ^+v!; ^+v!; ^+v!; ^+v!; ^+v\SL5_AHK_Refactor_engine\SL5_AHK_Refactor_engine\SL5_AHK_Refactor_engine
    Last_A_This:=A_ThisFunc . A_ThisLabel . " p"
    lll(A_LineNumber, "keysEveryWhere.ahk",Last_A_This)
	
    ToolTip1sec(A_LineNumber . " " . A_ScriptName . " " . Last_A_This)
; 
SetKeyDelay,80,80
		send,{Blind}
		Sleep,500
		 ;~ if(GetKeyState("ctrl", "P") )
		;~ {
			;~ ToolTip,:( oops 15-06-14_23-49
			;~ return
		;~ }CopyQ ahk_class QWidgetCopyQ ahk_class QWidget
		SetTitleMatchMode,2
		; {ShiftDown}^1{ShiftUp}
	DetectHiddenWindows,on
	Send,{CtrlDown}{ShiftDown}
	Loop,10
	{
		;~ ControlSend, , - CopyQ{ShiftDown}^1{ShiftUp},ahk_class QWidget- CopyQ
		;~ Sen- CopyQd,{S- CopyQhiftDown}^1{ShiftUp} 1runCopyQ_Ctrl_Shift_v1runCopyQ_Ctrl_Shift_v
		Suspend,on
		send,{Numpad1}1
		WinActivate,- CopyQ
		Sleep,100
		IfWinActive,- CopyQ
			break
	}
		Send,{ShiftUp}{CtrlUp} 
		Suspend,Off
		;~ MsgBox, :) great CopyQ is active 
	; CopyQCopyQ CopyQCopyQCopyQCopyQCopyQ{CtrlDown}{ShiftDown}1{ShiftUp}{CtrlUp}{CtrlDown}{- CopyQShiftDown}1{ShiftUp}{CtrlUp}
	WinSet, AlwaysOnTop,On,- CopyQ ; Toggle the always-on-top status of Calculator.
    WinWaitActive, - CopyQ ,,2
    if !WinExist("- CopyQ")
      MsgBox, please install CopyQ and add a global hotkey STRG+SHIFT+1 (v is not possible there - or?)

    WinWaitNotActive, - CopyQ
	; - CopyQ- CopyQ
	; cl- CopyQeanUp 
	Clipboard = %Clipboard% 
	
    return
}



;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
convert123To_NumPad123(t)
{
  StringReplace, t, t, 1 , {numpad1}, All 
  StringReplace, t, t, 2 , {numpad2}, All 
  StringReplace, t, t, 3 , {numpad3}, All 
  StringReplace, t, t, 4 , {numpad4}, All 
  StringReplace, t, t, 5 , {numpad5}, All 
  StringReplace, t, t, 6 , {numpad6}, All 
  StringReplace, t, t, 7 , {numpad7}, All 
  StringReplace, t, t, 8 , {numpad8}, All 
  StringReplace, t, t, 9 , {numpad9}, All 
  StringReplace, t, t, 0 , {numpad0}, All 
  return t
}  
;>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
lll(A_LineNumber, "functions_global.inc.ahk")
#Include ToolTipSec.inc.ahk ; ein kommentar
lll(A_LineNumber, "functions_global.inc.ahk")
#Include UPDATEDSCRIPT_global.inc.ahk
lll(A_LineNumber, "functions_global.inc.ahk")
