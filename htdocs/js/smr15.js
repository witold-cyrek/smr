function voteSite(a,b){window.open(a);window.location=b}var intervalCalc,intervalM,intervalRace;function calc(){var e,i,a,h,g,b,c,j,d,f=document.FORM;e=f.port1.value;i=f.port2.value;a=f.port3.value;h=f.port4.value;g=f.port5.value;b=f.port6.value;c=f.port7.value;j=f.port8.value;d=f.port9.value;f.total.value=(e*1)+(i*1)+(a*1)+(h*1)+(g*1)+(b*1)+(c*1)+(j*1)+(d*1)}function startCalc(){intervalCalc=setInterval(calc,1)}function stopCalc(){clearInterval(intervalCalc)}function calcM(){var n,e,h,u,d,p,f,s,o,c,i,l,a,j,k,m,t,g,r,b,q=document.FORM;n=q.mine1.value;e=q.mine2.value;h=q.mine3.value;u=q.mine4.value;d=q.mine5.value;p=q.mine6.value;f=q.mine7.value;s=q.mine8.value;o=q.mine9.value;c=q.mine10.value;i=q.mine11.value;l=q.mine12.value;a=q.mine13.value;j=q.mine14.value;k=q.mine15.value;m=q.mine16.value;t=q.mine17.value;g=q.mine18.value;r=q.mine19.value;b=q.mine20.value;q.totalM.value=(n*1)+(e*1)+(h*1)+(u*1)+(d*1)+(p*1)+(f*1)+(s*1)+(o*1)+(c*1)+(i*1)+(l*1)+(a*1)+(j*1)+(k*1)+(m*1)+(t*1)+(g*1)+(r*1)+(b*1)}function startCalcM(){intervalM=setInterval(calcM,1)}function stopCalcM(){clearInterval(intervalM)}function set_even(){var a=document.FORM;a.race1.value=12;a.race2.value=11;a.race3.value=11;a.race4.value=11;a.race5.value=11;a.race6.value=11;a.race7.value=11;a.race8.value=11;a.race9.value=11;a.racedist.value=100}function Racecalc(){var e,i,a,h,g,b,c,j,d,f=document.FORM;e=f.race1.value;i=f.race2.value;a=f.race3.value;h=f.race4.value;g=f.race5.value;b=f.race6.value;c=f.race7.value;j=f.race8.value;d=f.race9.value;f.racedist.value=(e*1)+(i*1)+(a*1)+(h*1)+(g*1)+(b*1)+(c*1)+(j*1)+(d*1)}function startRaceCalc(){intervalRace=setInterval(Racecalc,1)}function stopRaceCalc(){clearInterval(intervalRace)};