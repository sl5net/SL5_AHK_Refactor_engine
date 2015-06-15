;~ #Include UPDATEDSCRIPT_global.inc.ahk
;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
FileGetTime, ModiTime, %A_ScriptFullPath%, M
ModiTime_OLD:=ModiTime
UPDATEDSCRIPT:

    ; http://www.autohotkey.com/board/topic/81732-try-catch-doesnt-work/
    ;~ .. but here's how to suppress load-time "function not found" errors:
    blank := " "
    dot := "."
    quo ="0
;~ commaBlank := ", "
 msg:="A_LineNumber . "" "" . A_ScriptName . "" "" . Last_A_This . ""`nA_ScriptFullPath= "" . A_ScriptFullPath "
 ;~ MsgBox,%msg%
ToolTip1sec%blank%(A_ScriptFullPath)
;ToolTip1sec(A_LineNumber . " " . A_ScriptName . " " . Last_A_This . "`n" . ModiTime_OLD . ":=" . ModiTime )



  ;FileGetAttrib,attribs,%A_ScriptFullPath%
  FileGetTime, ModiTime, %A_ScriptFullPath%, M
  ;IfInString,attribs,A
  if(ModiTime_OLD > 0 AND ModiTime > ModiTime_OLD)
  {
  temp:=A_LineNumber . " " . A_ScriptName . " " . Last_A_This . "`n" . ModiTime_OLD . ":=" . ModiTime 
  ToolTip,%temp%
    ;~ preParser(A_ScriptDir, A_ScriptName, A_ScriptFullPath)
    Sleep,500           
    
    UpdaSplashTit = Updated script: %A_LineNumber%
    
    FileSetAttrib,-A,%A_ScriptFullPath%
    ;~ SplashTextOn,w,h,tit,tex
    SplashTextOn,,,%UpdaSplashTit%,%UpdaSplashTit%
    Sleep,500
       ;~ only one SplashText window per script is possible.
  SplashTextOff,%UpdaSplashTit%

    Reload      ; Script wird neu geladen,neu ausgef�hrt
  }
  ModiTime_OLD:=ModiTime
  ;Last_A_This:=A_ThisFunc . A_ThisLabel 
  ;ToolTip1sec(A_LineNumber . " " . A_ScriptName . " " . Last_A_This)
  ; sicherheitshallber mach ich das noch in den update script timer
  
Return
;>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


#Include ToolTipSec.inc.ahk 
#Include ToolTipSec_RemoveToolTip.inc.ahk 
