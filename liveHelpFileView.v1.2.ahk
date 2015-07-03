;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
;~ please use this ! as first line in every script before all includes! :)
isDevellopperMode=true ; enthällt auch update script.
;>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
#Include init_global.init.inc.ahk

#Persistent
#SingleInstance,force

;~ SetTimer,reloadLabel,120000

;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
; if you press  letter or curser move script will open help file and get cursor back
; F1 will be triggerd as soon something is typed and idle time is more then 650 or so
; helpful during screencast or if you often use helpfile
;>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

doMoveCursor:=false
typedGlobal:=""

setbatchlines -1
SetTitleMatchMode,2

is_set_AutoHotkeyHelp_AlwaysOnTop := false
Loop,
{
  is_set_AutoHotkeyHelp_AlwaysOnTop := set_AutoHotkeyHelp_AlwaysOnTop(is_set_AutoHotkeyHelp_AlwaysOnTop)
  
  WinGetActiveTitle,at
  WinGetClass,ac,%at% 
  atc := at . " ahk_class " . ac 
  ;~ ToolTip,%A_TimeIdle% := A_TimeIdle `n
  ;~ ToolTip,%typedGlobal%
  ;~ if(  InStr(atc,"ahk_class SciTEWindow") && A_TimeIdle > 650  ){
  if(  InStr(atc,"ahk_class SciTEWindow") && A_TimeIdle > 4000  ){
    ;~ p := getMousePos()
    
    ;~ if ( p["x"] != pB["x"r] or  p["y"] != pB["y"]  ){
      ; mouse move recognition
      ;~ pB := p
      ;~ Sleep,700
      
      ;~ continue
    ;~ }
    if(StrLen(typedGlobal)<1)
      continue
    
    typedGlobal:=""
    
				;~ ToolTip % rArea["l"] . " " rArea["t"] . " `n " rArea["r"] . " " rArea["b"] . "  `n  `n " p["x"] . " " p["y"] . " " 
    
    if(doMoveCursor==true){
      SetKeyDelay,0,0
      Send,{Left 3}
    }
    SetKeyDelay,0,30
    Send,{f1}
    ;~ ToolTip,f1 58
    Sleep,10
    ; fight focus back
  ;~ rightNum:=3

  
  loop
  {
    WinActivate,%at%  
    IfWinActive,%at%
    {
      if(doMoveCursor==true){
        SetKeyDelay,0,0 
        Send,{Right 3}
      }
      goto, break_outer32
    }
    Sleep,10
  } 

break_outer32: 

  fightFocusBack(at)
  Sleep,500
 }
}
; hjkl
return

letterPressed(l,typedGlobal){
  ;~ global typedGlobal
  typedGlobal .= l ; 
  ;~ If(l = " "){
    ;~ openHelpFile(typedGlobal)
    ;~ typedGlobal:=""
  ;~ }
  return typedGlobal
}


#IfWinActive, ahk_class SciTEWindow
  SciTEWin := "ahk_class SciTEWindow"

~LButton::
return ; todo : problem: its closing submenues :( so its disturbing often.
      WinGetActiveTitle,at
  WinGetClass,ac,%at% 
  atc := at . " ahk_class " . ac 

    Send,{Blind}
    Sleep,100
    ;~ SetKeyDelay,delay,pressduration
    SetKeyDelay,40,40
    ;~ Suspend,ontestTestTest.
    Suspend,on
    IfWinActive,%SciTEWin%
      Send,{f1}
        ToolTip,f1 114

    ;~ SendInput,{f1}
    Suspend,off
    ;~ SendPlay,{f1}
    ;~ Send,{f1}
  
    ;~ Send,{Left}{Right} ; that shoud trigger F1 - its workaround
    ;~ Suspend,off
  
    SetKeyDelay,-1,-1
    ;~ Last_A_This:=A_ThisFunc . A_ThisLabel
    ;~ ToolTip1sec(A_LineNumber . " " . A_ScriptName . " " . Last_A_This)
    

    fightFocusBack(at)
return

;~ fightFocusBack(at)

fightFocusBack(at){
WinWaitNotActive,%at%,,2
  lll(A_LineNumber, "liveHelpFileView.v1.2.ahk",Last_A_This)
  Loop
  {
    IfWinNotExist,%at%
      return
      IfWinNotActive,%at%
        WinActivate,%at%
      ;~ WinWaitActive,%at%,,1
      ;~ IfWinActive,%at%
      ;~ {
        ;~ Goto, break_outer40
        ;~ ToolTip,fight for focus
        ;~ lll(A_LineNumber, "liveHelpFileView.v1.2.ahk",Last_A_This)
      ;~ }
      Sleep,40
      ;AutoHotkey Help ahk_class HH Parent 
 ; w=1280,
 ; x=428,y=28,t=0x241932
      IfWinActive,%at%
      {
        Send,{Esc} ; if text search is open it jumps to text search. ugly. 15-06-14_22-46
        goto,break_outer40
      }
    }
    break_outer40: 
    lll(A_LineNumber, "liveHelpFileView.v1.2.ahk",Last_A_This)
    Sleep,700   
  }


if(true){
  
#IfWinActive,ahk_class SciTEWindow  ; x=588,y=22,t=0xf19d8
~*a::typedGlobal := letterPressed("a",typedGlobal) 
~*b::typedGlobal := letterPressed("b",typedGlobal) 
~*c::typedGlobal := letterPressed("c",typedGlobal) 
~*d::typedGlobal := letterPressed("d",typedGlobal) 
~*e::typedGlobal := letterPressed("e",typedGlobal) 
~*f::typedGlobal := letterPressed("f",typedGlobal) 
~*g::typedGlobal := letterPressed("g",typedGlobal) 
~*h::typedGlobal := letterPressed("h",typedGlobal) 
~*i::typedGlobal := letterPressed("i",typedGlobal) 
~*j::typedGlobal := letterPressed("j",typedGlobal) 
~*k::typedGlobal := letterPressed("k",typedGlobal) 
~*l::typedGlobal := letterPressed("l",typedGlobal) 
~*m::typedGlobal := letterPressed("m",typedGlobal) 
~*n::typedGlobal := letterPressed("n",typedGlobal) 
~*o::typedGlobal := letterPressed("o",typedGlobal) 
~*p::typedGlobal := letterPressed("p",typedGlobal) 
~*q::typedGlobal := letterPressed("q",typedGlobal) 
~*r::typedGlobal := letterPressed("r",typedGlobal) 
~*s::typedGlobal := letterPressed("s",typedGlobal) 
~*t::typedGlobal := letterPressed("t",typedGlobal) 
~*u::typedGlobal := letterPressed("u",typedGlobal) 
~*v::typedGlobal := letterPressed("v",typedGlobal) 
~*w::typedGlobal := letterPressed("w",typedGlobal) 
~*x::typedGlobal := letterPressed("x",typedGlobal) 
~*y::typedGlobal := letterPressed("y",typedGlobal) 
~*z::typedGlobal := letterPressed("z",typedGlobal) 
~Space::typedGlobal := letterPressed(" ",typedGlobal) 
;~ ~*ä::typedGlobal := letterPressed("ä",typedGlobal) 
;~ ~*ö::typedGlobal := letterPressed("ö",typedGlobal) 
;~ ~*ü::typedGlobal := letterPressed("ü",typedGlobal) 
~*Left::typedGlobal := letterPressed("Left",typedGlobal) 
~*Right::typedGlobal := letterPressed("Right",typedGlobal) 
~*Up::typedGlobal := letterPressed("Up",typedGlobal) 
~*Down::typedGlobal := letterPressed("Down",typedGlobal) 
;~ ~*Click::typedGlobal := letterPressed("Click",typedGlobal) 
}

 
set_AutoHotkeyHelp_AlwaysOnTop(isOnTop){
  if(isOnTop==true)
    return isOnTop
  autoHotkey_Help_ahk = AutoHotkey Help ahk_class HH Parent
  ;~ autoHotkey_Help_ahk := AutoHotkey Help
    IfWinNotExist,%autoHotkey_Help_ahk%
    {
      ;~ MsgBox, :( %autoHotkey_Help_ahk%
      return false
    }
    WinSet, AlwaysOnTop, On,%autoHotkey_Help_ahk%
    ;~ isOnTop:=true
    return true
    
}

reloadLabel:
; buggy if not reloadet after a while. its workAround. 15-06-12_11-01 :)
Reload
return

#Include functions.inc.ahk
;<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
;~ subroutinen beispielsweise müsen ans Dateiende
#Include functions_dateiende.inc.ahk
;>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
