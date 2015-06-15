ScriptNameLetter := SubStr(A_ScriptName, 1 , 1)
ScriptNameLetter2 := SubStr(A_ScriptName, 1 , 2)
;~ tatam
iconAdress=%HardDriveLetter%:\fre\public\Graf-Bilder\icon\abc123\%ScriptNameLetter2%.ico

ifexist,%iconAdress%
	Menu, Tray, Icon, %iconAdress%
else
  ToolTip,http://www.branchenbuch-weltweit.dk/img/abc/a.png
  
