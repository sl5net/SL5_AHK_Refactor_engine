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
   MsgBox,,ShortCut CopyQ,Ctrl Shift V `n  dont work actually. `n please use Ctrl Shift 1. `n Sorry about that. thanks. 15.06.2015 , 3
   ;~ MsgBox [, Options, Title, Text, Timeout]
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
drawButtons(str , fontSize){
   ;~ Schriftname: Keycaps
   ;~ Version: Publisher's Paradise -- Media Graphics International Inc.
   ;~ Keycaps Regular.ttf; 
   
   ;~ Clipboard := A_ComputerName ; T540P-SL5NET
   ;~ MsgBox,%A_ComputerName% = A_ComputerName (line:%A_LineNumber%) `n 
   if("T540P-SL5NET" != A_ComputerName) ; 
      return
   ; maximal button size, recomandet:  drawButtons("1234567980" , 260)
   
   strLen := StrLen(str) + 1 ; some extra letters becouse some buttons are larger strg or so
   if(strLen<1)
      return false
   bx36 := 170
   by36 := 52
   W36 := 51
   H36 := 52
   R36 := 5 
   ;    ;~ WinSet, Region, 142-62 W102 H102 R20-20, Shortcut  ; Same as above but with corners rounded to 40x40.
   bx72:= 142
   by72 := 62
   W72 := 105
   H72 := 102
   R72 := 20
   
   m := (72 - 36) / (W72-W36) 
   W0 :=  72 - m * W72
   WFS1  := Round( (fontSize - W0) / m ) 
   WFS  := WFS1 *  strLen   + Round(  WFS1 * ( strLen - 1 )  * 0.3 )
   ;~ WFS  := Round( (fontSize - W0) / m) ; * StrLen(str+1) 
   ; ´; 284-62 W105 H102 R20-20
   ; 284-62 W105 H102 R20-20
   ; 284-62 W315 H102 R20-20
   
   ; 0-0 W774 H363 R98-98
   
   m := (72 - 36) / (bx72 - bx36) 
   bx0 :=  72 - m * bx72
   ;~ bxFS  := Round( (fontSize - bx0) / m  + ( WFS - WFS1) / 2 
   bxFStemp  := Round( (fontSize - bx0) / m )
   bxFS  := Round(bxFStemp  - WFS / 2 )
   bxFS  := -100
   bxFS  := 10
   ; -100-0 W774 H363 R98-98
   
   ;~ bxFS  := Round( WFS / 2 )
   
   m := (72 - 36) / (by72 - by36) 
   by0 :=  72 - m * by72
   byFS  := Round( (fontSize - by0) / m ) 
   ;~ byFS := 0
   
   m := (72 - 36) / (H72-H36) 
   H0 :=  72 - m * H72
   HFS  := Round(  (fontSize - H0) / m )
   ;~ HFS = 300
   
   m := (72 - 36) / (R72-R36) 
   R0 :=  72 - m * R72
   RFS  := Round(  (fontSize - R0) / m )
   Progress, ZX0 ZH60 m2  fs%fontSize% zh0 CTFFFFFF CW000000, %str% ,, Shortcut , Keycaps Regular
   SetTitleMatchMode,2
   CoordMode, Pixel , Screen
   CoordMode, Caret , Screen
   WinGetPos, x , y , w , h , SciTE4AutoHotkey
   
   ; top of scite window
   WinMove, Shortcut, , % x , % y - 70 , % WFS + 50, % HFS + 100
   
   ; bottom of scite window
   ;~ WinMove, Shortcut, , % x , % (y + w - HFS - byFS - 350) , % WFS + 50, % HFS + 100
   
   ;~ Progress, fs%fontSize% CTFFFFFF CW000000, %str% ,, Shortcut , Keycaps Regular
   ;~ temp=%bx36%-%by36% W%W36% H%H36% R%R36%-%R36%
   temp= %bxFS%-%byFS% W%WFS% H%HFS% R%RFS%-%RFS%
   
   ;~ Clipboard = `; %temp% 
   ;~ WinMove
   ;~ Sleep,2500
   x:=""
   y:=""
   WinSet, Region, %temp%, Shortcut  ; Same as above but with corners rounded to 40x40.
   
   SetTimer,ButtonsOffLabel,2000
   return   
   ;~ Progress, ZX0 ZH60 m2  fs36 zh0 CTFFFFFF CW000000,L,, Shortcut , Keycaps Regular
   ; 170-52 W51 H52 R5-5
   ;                             170-52 W51 H52 R5-5
   ;                             284-62 W0 H102 R20-20
   ;~ WinSet, Region, 170-52 W51 H52 R5-5, Shortcut  ; Same as above but with corners rounded to 40x40.
   
   ;~ Progress, ZX0 ZH60 m2  fs72 zh0 CTFFFFFF CW000000,L,, Shortcut , Keycaps Regular
   ;~ WinSet, Region, 142-62 W102 H102 R20-20, Shortcut  ; Same as above but with corners rounded to 40x40.
   ;~ return WinSet, Region, 142-62 W102 H102 R20-20, Shortcut  ; Same as above but with corners rounded to 40x40.
   
   
   ;~ Progress, ZW-1 ZX0 ZH600 m2 b fs36 zh0 CTFF0000 CW0000FF,   L=l M=m N=n  O=o P=p Q=q R=r S=s T=t U=u V=v W=w  X=x Y=y Z=z   , , , Keycaps Regular
   ;~ Progress, ZW-1 ZX0 ZH600 m2 b fs24 zh0 CTFF0000 CW0000FF, \u232B x <  ÄÖÜ äöü <= x /  = ~ `` ´ ³ ° _ - : . `; `, ' " $ & ( ) { } µ   ß ? \  ( ] [ ² @  | `%  * + ü ö ä # * ~  a b c d e f g h  i j k  Q  < > , , , Keycaps Regular
}

ButtonsOffLabel:
   SetTimer,ButtonsOffLabel,Off
   ;~ SplashImage, Off
   Progress, Off
return

; 15-07-07_16-22 
lll(A_LineNumber, "functions_global.inc.ahk")
#Include ToolTipSec.inc.ahk ; ein kommentar
lll(A_LineNumber, "functions_global.inc.ahk")
#Include UPDATEDSCRIPT_global.inc.ahk
lll(A_LineNumber, "functions_global.inc.ahk")