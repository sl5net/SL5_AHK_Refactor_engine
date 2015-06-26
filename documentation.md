# SL5_AHK_Refactor_engine
SL5_AHK_Refactor_engine is a developer productivity engine for shk developers with scite4ahk

<pre>

/* SL5_AHK_Refactor_engine is free for personal and commercial use.
 */

features:

example how to read this short doc:

strg c
selection > clipboard

strg v
clipboard > selection

strg minus


strg d 
double line

strg shift b 
/* */

strg alt m
selection > method

strg alt v (using the line)
examples:
1 > d_1 := 1
123 > d_123 := 123
abc > abc = %abc%
%abc% > abc = %abc%
hi all> hi_all = hi all
strLen_mouse := StrLen( "mouse") ;
tim = %tim%

strg + enter
hi("  > hi(" ") 
hi("jim > hi("jim") 

strg + % (its using clipboard content)
clipboard > %clipboard% = clipboard (line:%A_LineNumber%) `n 
all behind %clipboard% is selected. so you simly could delete it.

shift j ( changes to carret position)
carret > %carret% 
%carret% > %carret% 
%carret% > "carret" 
"carret" > carret 

shift alt c (changes to line)
123456 > 1, 2, 3, 4, 5, 6, 
1, 2, 3, 4, 5, 6, > 1: 2: 3: 4: 5: 6:
1: 2: 3: 4: 5: 6: > 1, 2, 3, 4, 5, 6,

str shift up / down
line up / line down

win left / right
move to last / next functions navigation

alt c (changes word at your carret. dont need to select or to copy. )
kjhkjh > %kjhkjh% 
%kjhkjh% > "kjhkjh"
"kjhkjh" > kjhkjh 

strg shift BackSpace
jumps to last edit position

strg b
jumps to definition

second line of keyboard
1>! 2>" 3>§ 4>$ 5>% .....

tab
line tab and down

strg shift v
opens Clipboard history for the last 20 or so (using copyQ portable version)
https://github.com/hluk/CopyQ/releases/download/v2.4.7/copyq-windows-2.4.7.zip
please install CopyQ and add a global hotkey STRG+SHIFT+1 (v is not possible there - or?)

 Reformatting Source Code via Ctrl+Alt+L
 You can reformat source code via Shortcut Ctrl+Alt+L. its will lay out spacing, indents etc. 
+Reformatting actually formats the entire file.


lll
lll(A_LineNumber, __DIR __ __FILE __,Last_A_This)
it writes to logfiles in log directory
not inside online versione jet 15-06-14_20-27

peprocessor actions:
__DIR __ replacing with subfolders name
__FILE __ replacing with fileName 
not inside online versione jet 15-06-14_20-27

BTW preprocessor also looks inside subdir scripts, copes includes inside and corrects includes path... and much more.
not inside online versione jet 15-06-14_20-27

AutoUpdate
script offers autoupdate (not implementeed totally jet. 15-06-14_20-27)

autoRun if saved
not in online version now 15-06-14_20-42

autoSave if iddle
not in online version now 15-06-14_20-42

automatically creates script icons with significant letters of the script
not in online version now 15-06-14_20-42

strg shift z
redo 

F1 obens autohotkey help
clicking or movoning carret past idle time open autohotkey help
ö opens gÖögle ;) gooogle help.

enjoy, many thanks so many people for so great tool and help. thanks for help, bug reports and much more :)
best regards Se from SL5
