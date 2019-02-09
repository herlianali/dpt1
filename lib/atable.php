<?php
$GLOBALS['atablenum']=0;
class Atable {
	// ==== param init
	var $query; var $col; var $colv; var $limit; var $limitfind; var $orderby; var $groupby; var $where; var $addvar; var $param; var $addlastrow; var $colsize; var $colalign; var $caption; var $style; var $showsql;
	var $debug=TRUE; var $colnumber=TRUE;
	var $searchbar=TRUE; var $datainfo=TRUE; var $paging=TRUE;
	var $reload=FALSE; var $collist=FALSE; var $xls=FALSE;
	var $querysql;
	var $database;
	var $linkDB="";var $dbcon="";

	var $add=FALSE;var $edit=FALSE;var $delete=FALSE;var $proctbl=FALSE;

	function load(){
		if(empty($this->database)){
			if($this->dbcon==""){
				foreach(array_reverse($GLOBALS) as $variable){
					if(is_resource($variable) && get_resource_type($variable)=='mysql link'){
						$this->linkDB = "mysql";
						$this->dbcon=$variable;
						break;
					}else if(is_object($variable)  && get_class($variable)=='mysqli'){
						$this->linkDB = "mysqli";
						$this->dbcon=$variable;
						break;
					}else if(is_resource($variable) && get_resource_type($variable)=='pgsql link'){
						$this->linkDB = "pgsql";
						$this->dbcon=$variable;
						break;
					}
				}
			}
		}else{
			if(empty($this->linkDB)){
				$this->linkDB = $this->database;
			}
		}
		// ==== param init
		$qrytable = $this->query;
		$atablecol = json_decode($this->col);
		$atablecolv = json_decode($this->colv);
		$limit = !empty($this->limit)?$this->limit:10;
		$limitfind = !empty($this->limitfind)?$this->limitfind:$limit;
		$orderby = !empty($this->orderby)?$this->orderby:'';
		$groupby = !empty($this->groupby)?$this->groupby:'';
		$where = !empty($this->where)?$this->where:'';
		$colnumber = isset($this->colnumber)?$this->colnumber:TRUE;
		$addvar = !empty($this->addvar)?json_decode($this->addvar,true):'';
		$param = !empty($this->param)?$this->param:'';
		$addlastrow = !empty($this->addlastrow)?$this->addlastrow:'';
		$colsize = !empty($this->colsize)?json_decode($this->colsize):'';
		$colalign = !empty($this->colalign)?json_decode(strtoupper($this->colalign)):'';
		$showsql = !empty($this->showsql)?$this->showsql:'';
		$caption = !empty($this->caption)?$this->caption:'';
		$style = !empty($this->style)?$this->style:'table table-hover';
		$lblcol = array();
		$sortpost = "";
		$sqlerror = FALSE;
		$getcoltable=preg_replace("/ as [\s\S]+? /"," ",preg_replace("/ as [\s\S]+?,/",",",$this->GetBetween($qrytable,"select","from")));
		// ===============================


		$tblnm= trim(str_replace("from ","",substr($qrytable,strpos($qrytable,"from"),strlen($qrytable))));
		if(strpos($tblnm,' ') !== false){
		  $this->proctbl=FALSE;
		}else{$this->proctbl=TRUE;}
		if(isset($_POST['atable_process_data'])){
			if($GLOBALS['atablenum']==$_POST['process_table']){
			  $npd=0;$recdt="";
			  if($_POST['atable_process_data']=='edit'){
			    $qryp= "update $tblnm set ";
			    foreach ($_POST['vdata'] as $key => $value) {
			      if($npd>0){$qryp.=',';$recdt.=' AND ';}$npd++;
			      $qryp.=$key."='".$value."'";
						if($_POST['ndata'][$key]==""){
				      $recdt.="(".$key."='' OR ".$key." is null)";
						}else{
				      $recdt.=$key."='".$_POST['ndata'][$key]."'";
						}
			    }
			    $qryp.=" where ".$recdt;
			  }else if($_POST['atable_process_data']=='delete'){
			    $qryp= "delete from $tblnm where ";
			    foreach ($_POST['vdata'] as $key => $value) {
			      if($npd>0){$recdt.=' AND ';}$npd++;
						if($_POST['ndata'][$key]==""){
				      $recdt.="(".$key."='' OR ".$key." is null)";
						}else{
							$recdt.=$key."='".$_POST['ndata'][$key]."'";
						}
			    }
			    $qryp.=$recdt;
			  }else if($_POST['atable_process_data']=='add'){
			    $qryp= "insert into $tblnm (";
			    foreach ($_POST['vdata'] as $key => $value) {
			      if($npd>0){$qryp.=',';$recdt.=', ';}$npd++;
			      $qryp.=$key;
			      $recdt.="'".$value."'";
			    }
			    $qryp.=") values (".$recdt.")";
			  }
			  //echo $qryp;
			  $sts=$this->db_query($qryp);
			  if($sts){echo " atable_process_true";}else{echo " atable_process_false";}
			  exit;
			}
		}

		$theatable= '<div class="atable">'.($this->linkDB == ''?'<div class="warningdb">Atable Unknown Database connection.</div>':'').'<div class="atablepreloader" id="atablepreloader'.$GLOBALS['atablenum'].'"><span>Loading ....</span></div>
		<div class="findfield" style="padding:0px 5px;min-width:200px;z-index:3;"><input type="text" class="txtfind" name="find" placeholder="Search" id="txtfind-'.$GLOBALS['atablenum'].'" onkeyup="atable_txtfind(this)"><div class="fndclear" onclick="clearsrc('.$GLOBALS['atablenum'].')">&times;</div></div>
			<div class="atform" id="atform'.$GLOBALS['atablenum'].'"></div>
			<div class="dtatable" id="dtatable'.$GLOBALS['atablenum'].'">';
		if(isset($_POST['atabledata'.$GLOBALS['atablenum']]) && isset($_POST['fromatable'])){
			if(isset($_POST['sortby'])){
				if($_POST['sortby']!=""){
					$orderby=$_POST['sortby'];
					$sortpost=$_POST['sortby'];
				}
			}
			if(empty($limit)){$limit=10;}
			if(!empty($addvar)){extract($addvar);}
			if(!empty($orderby)){$orderby='ORDER BY '.$orderby;}
			if(!empty($groupby)){$groupby='GROUP BY '.$groupby;}else{if($getcoltable!=' * '){$groupby='GROUP BY '.$getcoltable;}}
			if(!empty($where)){$where='HAVING '.$where;}
			$theatable.= '<div class="atablewrap" id="atablewrap'.$GLOBALS['atablenum'].'">
						<table class="'.$style.'" id="dtblatable'.$GLOBALS['atablenum'].'" border="0">
    				<caption>'.$caption.'</caption>
    				<thead>';
    				$atr=0;$nrospn=0;$colsrown=array();
    				foreach ($atablecolv as $vth) {if(is_array($vth)){$atr++;}}
    				if($atr==0){$theatable.= '<tr>';$theatable.= ($colnumber==TRUE?'<th width="1px"'.($atr>0?' rowspan="'.$atr.'"':'').'>No</th>':'');}
    				$sortpost = explode(" ",$sortpost);
    				foreach($atablecolv as $key=>$acolv){
    					if(is_array($acolv)){
								$nrospn=count($atablecolv);
    						$theatable.= '<tr>';
                if($key==0){$theatable.= ($colnumber==TRUE?'<th width="1px"'.($atr>0?' rowspan="'.$atr.'"':'').'>No</th>':'');}
    		        $vthn=$vth[0];$colrown=array();$colrowv=array();$arrkey=0;$ncolss=0;
    						foreach ($acolv as $keyf => $vth) {
    				      $vthn=$vth;$colrow='';$colsz='';$colalgn='';
    				      if(is_array($vth)){
    				        $vthn=$vth[0];
    				        foreach ($vth as $keyn => $value) {
    				          if($value!='' && $keyn!=0){
    										if(strtolower(substr($value,0,1))=='w'){
    											$colsz=' width="'.substr($value,1).'"';
    										}else if(strtolower($value)=='ac'){
    											$colalgn=' style="text-align:center;"';
    										}else if(strtolower($value)=='al'){
    											$colalgn=' style="text-align:left;"';
    										}else if(strtolower($value)=='ar'){
    											$colalgn=' style="text-align:right;"';
    										}else{
    					            $colrown[$keyf]=substr($value,3);
    					            $colrowv[$keyf]=strtolower(substr($value,0,3));

							            if($colrowv[$keyf]=='col'){$ncolss=$ncolss+$colrown[$keyf];$arrn=$ncolss;}
							            if($colrowv[$keyf]=='row'){
							              if($colrowv[$keyf-1]!='row'){$arrn++;}
							              $ncolss++;
							              $colsrown[$key][$arrn-1]++;
							            }
    										}
    				            $colrow.=' '.$colrowv[$keyf].'span="'.$colrown[$keyf].'"';
    				          }
    				        }
    				      }

    							$theatable.= '<th'.$colsz.$colalgn.$colrow.'>';
									//$arrkey=($colrowv[$keyf-1]=='col' && $keyf>0?$keyf+$colrown[$keyf-1]-1:$arrkey);

									$arrkey=($colrowv[$keyf-1]=='col' && $keyf>0?$arrkey+$colrown[$keyf-1]-1:$arrkey);

									$nmcol= str_replace('$','',str_replace(';','',$atablecol[$arrkey]));
									$existcol= $this->GetBetween($qrytable,"select","from");
									if(strpos($existcol,$nmcol)!==false){
										$bysort = $nmcol;
									}else{
										$bysort = $vthn.';';
									}
									if($sortpost[0]==$bysort){
										if($sortpost[1]=='ASC'){
											$iconsort = '<span>&#9662;</span>';
										}else{
											$iconsort = '<span>&#9652;</span>';
										}
									}else{
										$iconsort = '';
									}

									//$lblcol[$arrkey]=$vthn;
									if(array_key_exists($arrkey,$colsrown[$key-1])){
										$arrkey+=$colsrown[$key-1][$arrkey];
										$lblcol[$arrkey]=$vthn;
									}else{
										$lblcol[$arrkey]=$vthn;
									}

    							$theatable.= (strpos($bysort, ';')!==false||($colrowv=='col')?$vthn.$kk:$kk.'<a href="javascript:void(0);" id="sortby-'.$GLOBALS['atablenum'].'-'.$bysort.'" class="sortby" onclick="atable_sortedby(this);">'.$iconsort.'&nbsp;'.$vthn.'</a>');
    							$theatable.= '</th>';
									$arrkey++;
    						}

								if($key==0){
									if(($this->edit || $this->delete) && $this->proctbl){
										$theatable.= '<th'.(isset($colsize[count($colalign)-1])?' width="'.$colsize[count($colalign)-1].'"':'').(isset($colalign)?' style="text-align:'.($colalign[count($colalign)-1]=='R'?'right':($colalign[count($colalign)-1]=='C'?'center':'left')).';"':'').' rowspan="'.$nrospn.'">Action</th>';
									}
								}
								$kyrow++;
    						$theatable.= '</tr>';
    					}else{
    						$theatable.= '<th'.(isset($colsize[$key])?' width="'.$colsize[$key].'"':'').(isset($colalign)?' style="text-align:'.($colalign[$key]=='R'?'right':($colalign[$key]=='C'?'center':'left')).';"':'').$colrow.'>';
								$nmcol= str_replace('$','',str_replace(';','',$atablecol[$key]));
								$existcol= $this->GetBetween($qrytable,"select","from");
								if(strpos($existcol,$nmcol)!==false){
									$bysort = $nmcol;
								}else{
									$bysort = $atablecol[$key];
								}
								if($sortpost[0]==$bysort){
									if($sortpost[1]=='ASC'){
										$iconsort = '<span>&#9662;</span>';
									}else{
										$iconsort = '<span>&#9652;</span>';
									}
								}else{
									$iconsort = '';
								}
								$lblcol[$key]=$acolv;
    						$theatable.= (strpos($bysort, ';')!==false?$acolv:'<a href="javascript:void(0);" id="sortby-'.$GLOBALS['atablenum'].'-'.$bysort.'" class="sortby" onclick="atable_sortedby(this);">'.$iconsort.'&nbsp;'.$acolv.'</a>');
    						$theatable.= '</th>';
    					}
    				}
						ksort($lblcol);
    				if($atr==0){
							if(($this->edit || $this->delete) && $this->proctbl){
								$theatable.= '<th'.(isset($colsize[$key+1])?' width="'.$colsize[$key+1].'"':'').(isset($colalign)?' style="text-align:'.($colalign[$key+1]=='R'?'right':($colalign[$key+1]=='C'?'center':'left')).';"':'').$colrow.'>Action</th>';
								array_push($lblcol,'Action');
							}
							$theatable.= '</tr>';
						}else{
							if(($this->edit || $this->delete) && $this->proctbl){
								array_push($lblcol,'Action');
							}
						}
    	$theatable.= '</thead>
    			<tbody>';

    	$i = 1;
    	$per_page = $limit;
    	$datarecord = $this->db_num_rows($this->db_query($qrytable." ".$groupby." ".$where));
    	$jml_pages = ceil($datarecord/$per_page);
    	$pages = 1;

    	// get page
    	if(isset($_POST['h'])) {
    		$pages = $_POST['h'];
    		$i=$i+(($pages-1)*$per_page);
    	}

    	if(isset($_POST['afind'])){
				$afind = $_POST['afind'];
    		if($afind==''){
    			$per_page = $limit;
    			if(isset($_POST['showall'])){
    				$forlimit = "";
    				$jml_pages = 1;
    			}else{
    				$forlimit = " LIMIT $per_page OFFSET ".($pages-1) * $per_page;
    			}
    			$this->querysql = $qrytable." ".$groupby." ".$where." ".$orderby.$forlimit;
    			$qry = $this->db_query($this->querysql);
    			if($this->db_num_rows($qry)==0){
    				$theatable.= '<tr><td colspan="'.(count($atablecol)+1).'" style="font-weight:bold;text-align:center;">No Data.</td><tr>';
    			}
    		}else{
    			$afind=str_replace(' ','%',str_replace("'", "''", $afind));
    			if(strpos(strtolower($afind), '"')!==false){
    				$afind=str_replace('"','',preg_replace('/(?| *(".*?") *| *(\'.*?\') *)| +/s', '%$1', $afind));
    			}
    			$per_page = $limitfind;

    			if($where!=""){
    				$iswhere = ' AND ';
    			}else{
    				$iswhere = ' HAVING ';
    			}

    			if(isset($_POST['showall'])){
    				$forlimit = "";
    			}else{
    				$forlimit = " LIMIT $per_page OFFSET ".($pages-1) * $per_page;
    			}

    			$columnwhere="";
    			$colsrc=preg_replace("/,(?=[^)]*(?:[(]|$))/", ",' ',",$getcoltable);
    			if($colsrc==" * "){$colsrc=implode(",' ',",$atablecol);}
    			$colwhere=explode(",' ',",$colsrc);
    			$lencol=count($colwhere);
    			if($lencol>50){
    				$i=0;
    				$columnwhere.="(";
    				for($n=0;$n<ceil(count($colwhere)/50);$n++){
    					$arrhalf = array_slice($colwhere, $n+$i, 25*($n+1));

    					if($n>0){$columnwhere.=" OR ";}
    					$columnwhere.="lower(concat(";
    					foreach($arrhalf as $key => $value){
    						if($key!=0){$columnwhere.=",' ',";}
    						$columnwhere.=$value;
    						$i++;
    					}
    					$columnwhere.=")) LIKE '%".strtolower($afind)."%'";
    				}
    				$columnwhere.=")";
    			}else{
    				$columnwhere="lower(concat(".$colsrc.")) LIKE '%".strtolower($afind)."%'";
    			}
    			$this->querysql = $qrytable." ".$groupby." ".$where.$iswhere.$columnwhere." "." ".$orderby.$forlimit;
    			$qry=$this->db_query($this->querysql);

    			if($this->db_num_rows($qry)==0){
    				if(strpos(strtolower($qry), 'error')!==false && $this->debug){
    					$sqlerror = TRUE;
    					$theatable.= '<tr><td colspan="'.(count($atablecol)+1).'" style="color:#e74c3c;text-align:center;">'.$qry.'</td><tr>';
    				}else{
    					$theatable.= '<tr><td colspan="'.(count($atablecol)+1).'" style="font-weight:bold;text-align:center;">Not Found.</td><tr>';
    				}
    			}

    			$jml_pages = 1;
    		}
    	}else{
    		if(isset($_POST['showall'])){
    			$this->querysql = $qrytable." ".$groupby." ".$where." ".$orderby;
    			$qry = $this->db_query($this->querysql);
    			$jml_pages = 1;
    		}else{
    			$this->querysql = $qrytable." ".$groupby." ".$where." ".$orderby." LIMIT $per_page OFFSET ".($pages-1) * $per_page;
    			$qry = $this->db_query($this->querysql);
    		}

    		if($this->db_num_rows($qry)==0){
    			if(strpos(strtolower($qry), 'error')!==false && $this->debug){
    				$sqlerror = TRUE;
    				$theatable.= '<tr><td colspan="'.(count($atablecol)+1).'" style="color:#e74c3c;text-align:center;">'.$qry.'</td><tr>';
    			}else{
    				$theatable.= '<tr><td colspan="'.(count($atablecol)+1).'" style="font-weight:bold;text-align:center;">No Data.</td><tr>';
    			}
    		}
    	}

    	if($showsql || $sqlerror){
    		$theatable.= '<tr><td colspan="'.(count($atablecol)+1).'" style="text-align:center !important;color:#c1a;">'.$this->querysql.'</td></tr>';
    	}

    	if($qry){$continue=FALSE;$break=FALSE;
    		while($row=$this->db_fetch_object($qry)){
    			if(!empty($param)){eval($param);}
    			if($continue){$continue=FALSE;continue;}
    			if($break){$break=FALSE;break;}
    			$theatable.= '<tr>'.
    					($colnumber==TRUE?'<td data-label="No">'.$i.'</td>':'');
    					$nocols=0;
    					foreach($atablecol as $key=>$acol){
    						$theatable.= '<td '.(isset($colalign)?'style="text-align:'.($colalign[$nocols]=='R'?'right':($colalign[$nocols]=='C'?'center':'left')).';"':'').' data-label="'.$lblcol[$key].'">';
    							if(strpos($acol, ';')!==false){
    								eval('$theatable.='.$acol);
    							}else{
    								$theatable.= $row->$acol!=""?$row->$acol:"&nbsp;";
    							}
    						$theatable.= '</td>';
    						$nocols++;
    					}

							if(($this->edit || $this->delete) && $this->proctbl){
								$theatable.='<td '.(isset($colalign)?'style="text-align:'.($colalign[$nocols]=='R'?'right':($colalign[$nocols]=='C'?'center':'left')).';"':'').' data-label="'.$lblcol[count($lblcol)-1].'">';
									if($this->edit){$theatable.='<button type="button" class="btn btn-default btn-xs atedit" onclick=\'atable_processdata('.$GLOBALS['atablenum'].',this,"edit",'.$this->col.','.json_encode($lblcol).','.$colnumber.')\' style="font-size:18px;height:30px;"><span class="ic edit"></span></button>';}
									if($this->delete){$theatable.='<button type="button" class="btn btn-default btn-xs atdelete" onclick=\'atable_processdata('.$GLOBALS['atablenum'].',this,"delete",'.$this->col.','.json_encode($lblcol).','.$colnumber.')\' style="font-size:18px;height:30px;"><span class="ic trash"></span></button>';}
								$theatable.='</td>';
							}
    			$theatable.= '</tr>';
    			$i++;
    		}
    	}

		if(!empty($addlastrow)){eval('$theatable.='.$addlastrow);}
		$theatable.= '</tbody>
		</table></div>';

		$showpg=0;$class="";//$lblcol
		$theatable.= '<!-- datainfo -->
		<div class="colhide" id="colhide'.$GLOBALS['atablenum'].'">
		<div style="margin-bottom:6px;"><select multiple="multiple" style="width:250px;min-height:83px;max-height:120px;" id="slctmltp'.$GLOBALS['atablenum'].'" class="form-control">';
			if($this->colnumber){
				$theatable.= '<option value="0" selected="selected">No</option>';
			}
			foreach($lblcol as $key=>$lbl){
				$theatable.= '<option value="'.($this->colnumber?$key+1:$key).'" selected="selected">'.$lbl.'</option>';
			}
		$theatable.= '</select></div>
		<button type="button" class="btn btn-default btn-sm" id="colhidecancel" style="float:right" onclick="atable_showhide(\'colhide'.$GLOBALS['atablenum'].'\')">Cancel</button>
		<button type="button" onclick="atable_hidecol(\'dtblatable'.$GLOBALS['atablenum'].'\',getSelectMultiValues(\'slctmltp'.$GLOBALS['atablenum'].'\'),'.$GLOBALS['atablenum'].');atable_showhide(\'colhide'.$GLOBALS['atablenum'].'\')" class="btn btn-default btn-sm" id="colhideok" style="float:right">Ok</button>
		</div>
		<div class="datainfo">'.
		($this->add==TRUE && $this->proctbl?
		  '<button type="button" onclick=\'atable_processdata('.$GLOBALS['atablenum'].',this,"add",'.$this->col.','.json_encode($lblcol).','.$colnumber.')\' class="btn btn-primary btn-xs" title="Add Data" id="dtadd'.$GLOBALS['atablenum'].'" style="font-size:18px;height:30px;"><b>+</b></button>&nbsp;':'').
		($this->reload==TRUE?
		  '<button type="button" onclick="atable_reload('.$GLOBALS['atablenum'].')" class="btn btn-info btn-xs" title="Reload" id="dtreload'.$GLOBALS['atablenum'].'" style="font-size:18px;height:30px;">&#8635;</button>&nbsp;':'').
		($this->collist==TRUE?
		  '<button type="button" onclick="atable_showhide(\'colhide'.$GLOBALS['atablenum'].'\')" class="btn btn-default btn-xs" title="Column" id="dtlist'.$GLOBALS['atablenum'].'" style="font-size:18px;height:30px;">&#8862;</button>&nbsp;':'').
		($this->xls==TRUE?
		  '<button type="button" onclick="atable_toxls(\'dtblatable'.$GLOBALS['atablenum'].'\',\''.str_replace(" ","_",$caption).'\')" id="dtxls'.$GLOBALS['atablenum'].'" class="btn btn-success btn-sm" title="Export to Excel" id="dtxls">xls</button>&nbsp;':'').
		($this->datainfo==TRUE?
		((($i-1)==0?0:((($pages-1) * $per_page)+1))." to ".($i-1)." of ".$datarecord." data").
		'&nbsp;&nbsp;
		<a href="javascript:void(0);" id="showall-'.$GLOBALS['atablenum'].'" class="showall" onclick="atable_showall(this);">Show All</a>
		<a href="javascript:void(0);" id="showless-'.$GLOBALS['atablenum'].'" class="showless" style="display:none;" onclick="atable_showless(this);">Show Less</a>':'').'
		</div>
		<!-- paging -->
		<div class="paggingfield" '.($this->paging==TRUE?'':'style="display:none;"').'>
			<ul class="pagination">';
			if($pages>1){
				$theatable.= '<li '.$class.'><a href="javascript:void(0);" id="'.($pages-1).'-'.$GLOBALS['atablenum'].'" class="pages" onclick="atable_pages(\''.($pages-1).'-'.$GLOBALS['atablenum'].'\');">&laquo;</a></li>';
			}
			for($page = 1;$page <= $jml_pages;$page++){
				$page == $pages ? $class='class="active"' : $class="";
				if((($page >= $pages-2) && ($page <= $pages +2)) || ($page==1) || ($page==$jml_pages)){
					if(($showpg==1)&&($page !=2 )){$theatable.= '<li><a href="javascript:void(0);" class="gapdot">...</a></li>';}
					if(($showpg!=($jml_pages-1))&&($page == $jml_pages)){$theatable.= '<li><a href="javascript:void(0);" class="gapdot">...</a></li>';}
					if($page == $pages){$theatable.= '<li '.$class.'><a href="javascript:void(this);" id="'.$page.'-'.$GLOBALS['atablenum'].'" onclick="atable_pages(\''.$page.'-'.$GLOBALS['atablenum'].'\');">'.$page.'</a></li>';}
					else{$theatable.= '<li '.$class.'><a href="javascript:void(this);" id="'.$page.'-'.$GLOBALS['atablenum'].'" class="pages" onclick="atable_pages(\''.$page.'-'.$GLOBALS['atablenum'].'\');">'.$page.'</a></li>';}
					$showpg=$page;
				}
			}
			if($pages<$jml_pages){
				$theatable.= '<li '.$class.'><a href="javascript:void(0);" id="'.($pages+1).'-'.$GLOBALS['atablenum'].'" class="pages" onclick="atable_pages(\''.($pages+1).'-'.$GLOBALS['atablenum'].'\');">&raquo;</a></li>';
			}
			$theatable.= '</ul>
		</div>';

		}// end post
		$theatable.= "</div></div>";
		$GLOBALS['atablenum']++;
		return $theatable;
	}// end function

	function GetBetween($pool,$var1="",$var2=""){
		$pool=strtolower($pool);//exception
		$temp1 = strpos($pool,$var1)+strlen($var1);
		$result = substr($pool,$temp1,strlen($pool));
		$dd=strpos($result,$var2);
		if($dd == 0){
		  $dd = strlen($result);
		}

		return substr($result,0,$dd);
	}


  function db_query($qry){
  	$res = "";
  	if($this->linkDB=="mysql"){
  		$res = mysql_query($qry);
  		if(!$res){
  			$res = mysql_error();
  		}
  	}else if($this->linkDB=="mysqli"){
			if($this->dbcon!=''){
  			$res = mysqli_query($this->dbcon,$qry);
			}else{$res = mysqli_query($qry);}
  		if(!$res){
  			$res = mysqli_errno($this->dbcon);
  		}
  	}else if($this->linkDB=="pgsql"){
			if($this->dbcon!=''){
	  		$res = pg_query($this->dbcon,$qry);
			}else{$res = pg_query($qry);}
  		if(!$res){
  			$res = pg_last_error($this->dbcon);
  		}
  	}else if($this->linkDB=="ci"){
			$this->CI = & get_instance();
			$database=!empty($this->database)?$this->database:"db";
  		$res = $this->CI->$database->query($qry);
  		if(!$res){
  			$res = $this->CI->$database->_error_message();
  		}
  	}
  	return $res;
  }
  function db_fetch_object($qry){
  	$res = "";
  	if($this->linkDB=="mysql"){
  		$res = mysql_fetch_object($qry);
  	}else if($this->linkDB=="mysqli"){
  		$res = mysqli_fetch_object($qry);
  	}else if($this->linkDB=="pgsql"){
  		$res = pg_fetch_object($qry);
  	}else if($this->linkDB=="ci"){
			$civer=explode(".",CI_VERSION);
			if($civer[0]=="2"){
  			$res = $qry->_fetch_object();
			}else{
  			$res = $qry->unbuffered_row();
			}
  	}
  	return $res;
  }
  function db_num_rows($qry){
  	$res = "";
  	if($this->linkDB=="mysql"){
  		$res = mysql_num_rows($qry);
  	}else if($this->linkDB=="mysqli"){
  		$res = mysqli_num_rows($qry);
  	}else if($this->linkDB=="pgsql"){
  		$res = pg_num_rows($qry);
  	}else if($this->linkDB=="ci"){
  		$res = $qry->num_rows();
  	}
  	return $res;
  }
}

function atable_init(){
	if(!isset($_POST['fromatable'])){
	echo '<style>*{margin:0;padding:0;box-sizing: border-box;}
	.atable{font-family:Arial;font-size:14px;color:#333;display:list-item;list-style:none;clear:both;margin-top:10px;margin-bottom:80px;position:relative;}
	.atable .atablewrap{width:100%;}
	.atable .dtatable .table{margin-bottom:0px;}
	.atable .atablepreloader{
		position:absolute;
		width:100%;
		height:100%;
		display:none;
    justify-content: center;
    align-items: center;
		z-index:2;
		top:50px;
	}
	.atable .atablepreloader span{
		width: 200px;
		background: #337AB7;
		color:#ffffff;
		padding: 15px;
		text-align: center;
		font-weight: bold;
		border: 3px solid #337AB7;
		border-radius: 3px;
	}
	.atable a{color:#337ab7;text-decoration:none;}

	/* ======= table ============= */
	.atable .table{width:100%;border-collapse: collapse;}
	.atable .table>thead>tr>th, .atable .table>tbody>tr>th, .atable .table>tfoot>tr>th{
		padding: 8px;
		line-height: 1.37;
		vertical-align: bottom;
		text-align: left;
	}
	.atable .table>thead>tr>td, .atable .table>tbody>tr>td, .atable .table>tfoot>tr>td{
		padding: 8px;
		line-height: 1.37;
		vertical-align: top;
	}
	.atable .table>thead>tr>th {
		border-bottom: 2px solid #ddd;
	}
	.atable .table>tbody>tr>td {
		border-top: 1px solid #ddd;
	}
	.atable .dtatable .table, .atable .jdtatable .table{margin-bottom:0px;}
	/* ======= end table ============= */

	.atable .datainfo{
		left:0px;
		display:block;clear:both;float:left;
		right:0px;margin-top: 23px;margin-left: 5px;
	}
	.atable .warningdb{
		position:absolute;
		width:100%;
		padding:25px;
		font-size:18px;
		font-weight:bold;
		text-align:center;
		background:#eee;
		z-index:7;
	}

	.atable .form-control {
		display: block;
		padding: 6px 12px;
		font-size: 14px;
		line-height: 1.42857143;
		color: #495057;
		background-color: #fff;
		background-clip: padding-box;
		border: 1px solid #ced4da;
		border-radius: 4px;
		transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
		transition-property: border-color, box-shadow;
		transition-duration: 0.15s, 0.15s;
		transition-timing-function: ease-in-out, ease-in-out;
		transition-delay: 0s, 0s;
	}

	.atable .findfield {
		float:right;
		position:relative;
		display:inline-block;
	}
	.atable .txtfind{
		background-image: url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 250.313 250.313" style="enable-background:new 0 0 250.313 250.313;" xml:space="preserve" height="15px" width="15px"><g id="Search"><path style="fill-rule:evenodd;clip-rule:evenodd;fill:gray" d="M244.186,214.604l-54.379-54.378c-0.289-0.289-0.628-0.491-0.93-0.76 c10.7-16.231,16.945-35.66,16.945-56.554C205.822,46.075,159.747,0,102.911,0S0,46.075,0,102.911 c0,56.835,46.074,102.911,102.91,102.911c20.895,0,40.323-6.245,56.554-16.945c0.269,0.301,0.47,0.64,0.759,0.929l54.38,54.38 c8.169,8.168,21.413,8.168,29.583,0C252.354,236.017,252.354,222.773,244.186,214.604z M102.911,170.146 c-37.134,0-67.236-30.102-67.236-67.235c0-37.134,30.103-67.236,67.236-67.236c37.132,0,67.235,30.103,67.235,67.236 C170.146,140.044,140.043,170.146,102.911,170.146z"/></g></svg>\');
    background-position: 6px center;
    background-repeat: no-repeat;

		outline: none;
    height: 34px;
    padding-top: 6px;
    padding-bottom: 6px;
    padding-left: 26px;
    padding-right: 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
	}
	.txtfind:focus:not(:placeholder-shown) + div{display:table;}
	.txtfind:not(:placeholder-shown):hover + div{display:table;}
	.atable .fndclear{
		position: absolute;
		user-select: none;
    float: right;
    top: 23%;
    right: 10px;
    width: 16px;
    height: 18px;
    border-radius: 6px;
    background: #f1f1f1;
    color: white;
    font-weight: bold;
    text-align: center;
    cursor: pointer;
    font-size: 1em;
		display:none;
	}
	.atable .fndclear:hover {
		background: #ccc;
		display:table;
	}
	.atable .colhide{
		display:none;
		position: absolute;
		z-index: 2;
		margin-top: -115px;
		margin-left: 20px;
		background: #fff;
		padding: 10px;
		border-radius: 5px;
		box-shadow: 0px 0px 5px 0px #333;
	}
	.atable .table caption {
		color:#000;
		font-size: 1.5em;
		text-align: center;
	}
	.atable .paggingfield{
		float:right;
		margin:0px 5px;
	}
	.atable .paggingfield .pagination{
		display: inline-block;
		padding-left: 0;
		margin: 20px 0;
		border-radius: 4px;
	}
	.atable .paggingfield .pagination>ul{list-style-type: disc;}
	.atable .paggingfield .pagination>li{display: inline;}
	.atable .paggingfield .pagination>li:first-child>a{
	    border-top-left-radius: 4px;
	    border-bottom-left-radius: 4px;
	}
	.atable .paggingfield .pagination>li:last-child>a{
		border-top-right-radius: 4px;
		border-bottom-right-radius: 4px;
	}
	.atable .paggingfield .pagination>.active>a{
		color: #fff;
		cursor: default;
		background-color: #337ab7;
		border-color: #337ab7;
	}
	.atable .paggingfield .pagination>li>a{
		position: unset;
		float: left;
		padding: 6px 12px;
		margin-left: -1px;
		line-height: 1.42857143;
		color: #337ab7;
		text-decoration: none;
		background-color: #fff;
		border: 1px solid #ddd;
	}
	.atable .table caption {
		color:#000;
		font-size: 1.5em;
		text-align: center;
	}
	.atable .btn:focus{
		outline: none;
	}
	.atable .btn{
		display: inline-block;
		padding: 6px 12px;
		margin-bottom: 0;
		font-size: 14px;
		font-weight: 400;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		-ms-touch-action: manipulation;
		touch-action: manipulation;
		cursor: pointer;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		background-image: none;
		border: 1px solid transparent;
		border-radius: 4px;
		color: #333;
		background-color: #fff;
		border-color: #ccc;
	}
	.atable .btn-sm {
		padding: 5px 10px;
		font-size: 12px;
		line-height: 1.5;
		border-radius: 3px;
	}
	.atable .btn-xs {
		padding: 1px 5px;
		font-size: 12px;
		line-height: 1.5;
		border-radius: 3px;
	}
	.atable .btn-success {
		color: #fff;
		background-color: #5cb85c;
		border-color: #4cae4c;
	}
	.atable .btn-danger {
		color: #fff;
		background-color: #d9534f;
		border-color: #d43f3a;
	}
	.atable .btn-info {
		color: #fff;
		background-color: #5bc0de;
		border-color: #46b8da;
	}
	.atable .btn-primary {
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
	}
	.atable .btn-warning {
    color: #fff;
    background-color: #f0ad4e;
    border-color: #eea236;
	}

	.atable .atform{
	  position: absolute;
	  width: 100%;
	  height: 100%;
	  align-items: center;
	  justify-content: center;
	  display: none;
	}
	.atable .atform div{
	  margin: 99px auto;
	  display: table;
	  background: #fff;
	  box-shadow: 0px 0px 5px #333;
	  padding: 10px;
	  border-radius: 5px;
		max-width: 400px;
	}
	.atable .atform span{
	  font-weight: bold;
	}
	.atable .atform .atble{
	  margin-bottom: 10px;
	}
	.ic{
	  padding:9px;margin-top: 4px;position:relative;
	  background: url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="225"><defs><g id="empty"></g><g id="trash"><path d="m10.5,0.5c-0.52344,0 -1.05859,0.18359 -1.4375,0.5625c-0.37891,0.37891 -0.5625,0.91406 -0.5625,1.4375l0,1l-6,0l0,2l1,0l0,16c0,1.64453 1.35547,3 3,3l12,0c1.64453,0 3,-1.35547 3,-3l0,-16l1,0l0,-2l-6,0l0,-1c0,-0.52344 -0.18359,-1.05859 -0.5625,-1.4375c-0.37891,-0.37891 -0.91406,-0.5625 -1.4375,-0.5625l-4,0zm0,2l4,0l0,1l-4,0l0,-1zm-5,3l14,0l0,16c0,0.55469 -0.44531,1 -1,1l-12,0c-0.55469,0 -1,-0.44531 -1,-1l0,-16zm2,3l0,11l2,0l0,-11l-2,0zm4,0l0,11l2,0l0,-11l-2,0zm4,0l0,11l2,0l0,-11l-2,0z"/></g><g id="edit"><path d="m20.6483,0.73663c-0.91456,0 -1.77872,0.45368 -2.49886,1.16661l-14.95716,14.87794l-2.49886,7.48219l7.48219,-2.49167l0.15842,-0.15123l14.81314,-14.71951c0.71293,-0.72014 1.15942,-1.5843 1.15942,-2.49886c0,-0.91456 -0.44649,-1.77872 -1.15942,-2.49886c-0.72014,-0.71293 -1.5843,-1.16661 -2.49886,-1.16661zm-3.69429,4.95451l2.38364,2.39083l-12.15584,12.08384l-3.57186,1.18822l1.19543,-3.57905l12.14864,-12.08384z"/></g><g id="save"><path d="m19.72743,1.61357c-0.48519,-0.48519 -1.14376,-0.75819 -1.8295,-0.75819l-14.45486,0c-1.43617,0 -2.58769,1.16446 -2.58769,2.58769l0,18.11386c0,1.4297 1.15799,2.58769 2.58769,2.58769l18.11386,0c1.42323,0 2.58769,-1.16446 2.58769,-2.58769l0,-14.45486c0,-0.68574 -0.273,-1.34431 -0.75819,-1.8295l-3.659,-3.659zm-7.22743,19.94336c-2.14779,0 -3.88154,-1.73375 -3.88154,-3.88154s1.73375,-3.88154 3.88154,-3.88154s3.88154,1.73375 3.88154,3.88154s-1.73375,3.88154 -3.88154,3.88154zm2.58769,-12.93847l-10.35078,0c-0.7142,0 -1.29385,-0.57964 -1.29385,-1.29385l0,-2.58769c0,-0.7142 0.57964,-1.29385 1.29385,-1.29385l10.35078,0c0.7142,0 1.29385,0.57964 1.29385,1.29385l0,2.58769c0,0.7142 -0.57964,1.29385 -1.29385,1.29385z"/></g><g id="cross"><path d="m20.90875,18.89324l-2.01952,2.01575c-0.36855,0.3723 -0.97027,0.3723 -1.34258,0l-5.0469,-5.04315l-5.04314,5.04315c-0.37231,0.3723 -0.97779,0.3723 -1.34634,0l-2.0195,-2.01575c-0.37231,-0.37231 -0.37231,-0.97403 0,-1.34634l5.04314,-5.0469l-5.04314,-5.04314c-0.36856,-0.37608 -0.36856,-0.98155 0,-1.34634l2.0195,-2.0195c0.36855,-0.37231 0.97403,-0.37231 1.34634,0l5.04314,5.04689l5.0469,-5.04689c0.37231,-0.37231 0.97779,-0.37231 1.34258,0l2.01952,2.01575c0.3723,0.3723 0.3723,0.97779 0.00375,1.3501l-5.0469,5.04314l5.04315,5.0469c0.3723,0.37231 0.3723,0.97403 0,1.34634z"/></g></defs><use x="0" y="0" style="fill:white" xlink:href="%23empty"/><use x="0" y="25" style="fill:black" xlink:href="%23trash"/><use x="0" y="50" style="fill:white" xlink:href="%23trash"/><use x="0" y="75" style="fill:black" xlink:href="%23edit"/><use x="0" y="100" style="fill:white" xlink:href="%23edit"/><use x="0" y="125" style="fill:black" xlink:href="%23save"/><use x="0" y="150" style="fill:white" xlink:href="%23save"/><use x="0" y="175" style="fill:black" xlink:href="%23cross"/><use x="0" y="200" style="fill:white" xlink:href="%23cross"/></svg>\') no-repeat;
	  background-size: cover;
	  display: inline-block;
	}

	.trash{background-position: 50% 12.5%;}
	.trash-white{background-position: 50% 25%;}
	.edit{background-position: 50% 37.5%;}
	.edit-white{background-position: 50% 50%;}
	.save{background-position: 50% 62.5%;}
	.save-white{background-position: 50% 75%;}
	.cross{background-position: 50% 87.5%;}
	.cross-white{background-position: 50% 100%;}

	@media screen and (max-width: 550px) {
		.ic{position:unset;}
		.atable .table{
			border-collapse: collapse;
			table-layout: fixed;
			border: 0;
		}
		.atable .findfield{
			float:none;
			width:100%;
		}
		.atable .txtfind{
			width:100%;
		}
		.atable .table caption {
			font-size: 1.3em;
		}
		.atable .table thead {
			border: none;
			clip: rect(0 0 0 0);
			height: 1px;
			margin: -1px;
			overflow: hidden;
			padding: 0;
			position: absolute;
			width: 1px;
		}
		.atable .table tr {
			border-bottom: 3px solid #ddd;
			display: block;
			margin-bottom: .625em;
		}
		.atable .table td {
			border-top: 0px solid #ddd;
			display: block;
			font-size: .8em;
			text-align: right !important;
		}
		.atable .table td:before {
			content: attr(data-label);
			float: left;
			font-weight: bold;
		}
		.atable .table td:last-child {
			border-bottom: 0;
		}
		.atable .paggingfield {
			margin: 40px 5px;
		}
		.atable .paggingfield .pagination>li>a, .pagination>li>span {
			padding: 4px 5px;
		}
		.atable .paggingfield .pagination .gapdot {
			padding: 4px 4px;
		}
	}
	</style>
	<script>
	var xhr;
	var thepage="";
	var datapost={};
	var sortby=[];var ascdsc=[];var numpage=[];var colshowhide=[];
	var atable;var forEach;var atablests=[];
	(function($) {
		$(window).load(function() {});
		$(document).ready(function(e) {
			//declare
			atable = document.querySelectorAll(".dtatable");
			forEach = [].forEach;
			forEach.call(atable, function (el, i) {
				atable[i].insertAdjacentHTML("beforeBegin","");
				atable[i].insertAdjacentHTML("afterEnd","");
				atablests[i]=false;
			});

		});
	}) (jQuery);

	function atable_txtfind(me){
		var vid = me.id.split("-");
		xhr.abort();

		var v_afind = $("#txtfind-"+vid[1]).val();
		document.getElementById("atablepreloader"+vid[1]).style.display="flex";
		document.getElementById("showless-"+vid[1]).style.display="none";
		document.getElementById("showall-"+vid[1]).style.display="inline-block";

		var tbpage = Object.assign({}, datapost);
		numpage[vid[1]]=1;
		tbpage["atabledata"+vid[1]]=true;
		tbpage["sortby"]=sortby[vid[1]];
		tbpage["colshowhide"]=colshowhide[vid[1]];
		tbpage["fromatable"]=true;
		tbpage.afind=v_afind;

		xhr = $.ajax({
			type: "POST",
			url: thepage,
			data: tbpage,
			success: function(data){
				document.getElementById("atablepreloader"+vid[1]).style.display="none";
				var atableno=[];
				var htmldata = "<div>"+rbline(data)+"</div>";
				$(htmldata).find(".dtatable").each(function(i, obj){
					atableno[i]=this.innerHTML;
				});

				forEach.call(atable, function (el, i) {
					if(i==vid[1]){
						atable[i].innerHTML=atableno[i];
					}
				});
				atable_hidecol("dtblatable"+vid[1],colshowhide[vid[1]],vid[1]);
			}
		});
	}

	function atable_pages(val){
		xhr.abort();
		var vid = val.split("-");
		var v_afind = $("#txtfind-"+vid[1]).val();
		document.getElementById("atablepreloader"+vid[1]).style.display="flex";

		var tbpage = Object.assign({}, datapost);
		tbpage.h=vid[0];numpage[vid[1]]=vid[0];
		tbpage["atabledata"+vid[1]]=true;
		tbpage["sortby"]=sortby[vid[1]];
		tbpage["colshowhide"]=colshowhide[vid[1]];
		tbpage["fromatable"]=true;
		tbpage.afind=v_afind;
		//console.log(tbpage);
		xhr = $.ajax({
			type: "POST",
			url: thepage,
			data: tbpage,
			success: function(data){
				document.getElementById("atablepreloader"+vid[1]).style.display="none";
				var atableno=[];
				var htmldata = "<div>"+rbline(data)+"</div>";
				$(htmldata).find(".dtatable").each(function(i, obj){
					atableno[i]=this.innerHTML;
				});

				forEach.call(atable, function (el, i) {
					if(i==vid[1]){
						atable[i].innerHTML=atableno[i];
					}
				});
				atable_hidecol("dtblatable"+vid[1],colshowhide[vid[1]],vid[1]);
			}
		});
	};

	function atable_getpage(tableID){
		if(numpage[tableID]==undefined){
			numpage[tableID]=1;
		}
		return numpage[tableID];
	}

	function atable_topage(natbl,page){
		var fn=0;
		if(page<=0){page=1;}
		if(atablests[natbl]){
			atable_pages(page+"-"+natbl);
			numpage[natbl]=page;
		}
		$(document).ajaxStop(function(){
		  if(fn==0){
				atable_pages(page+"-"+natbl);
		    fn++;
		  }
		});
	}

	function atable_find(natbl,str){
		var fn=0;
		if(atablests[natbl]){
			$("#txtfind-"+natbl).val(str);
			$("#txtfind-"+natbl).keyup();
		}
		$(document).ajaxStop(function(){
		  if(fn==0){
		    $("#txtfind-"+natbl).val(str);
		    $("#txtfind-"+natbl).keyup();
		    fn++;
		  }
		});
	}

	function clearsrc(natbl){$("#txtfind-"+natbl).val("");$("#txtfind-"+natbl).keyup();$("#txtfind-"+natbl).focus();nxhrs=0;}

	function atable_showall(me){
		xhr.abort();
		var vid = me.id.split("-");
		var v_afind = $("#txtfind-"+vid[1]).val();
		document.getElementById("atablepreloader"+vid[1]).style.display="flex";

		var tbpage = Object.assign({}, datapost);
		tbpage.showall=true;
		numpage[vid[1]]=1;
		tbpage["atabledata"+vid[1]]=true;
		tbpage["sortby"]=sortby[vid[1]];
		tbpage["colshowhide"]=colshowhide[vid[1]];
		tbpage["fromatable"]=true;
		tbpage.afind=v_afind;
		console.log(tbpage);
		xhr = $.ajax({
			type: "POST",
			url: thepage,
			data: tbpage,
			success: function(data){
				document.getElementById("atablepreloader"+vid[1]).style.display="none";
				var atableno=[];
				var htmldata = "<div>"+rbline(data)+"</div>";
				$(htmldata).find(".dtatable").each(function(i, obj){
					atableno[i]=this.innerHTML;
				});

				forEach.call(atable, function (el, i) {
					if(i==vid[1]){
						atable[i].innerHTML=atableno[i];
					}
				});
				document.getElementById("showless-"+vid[1]).style.display="inline-block";
				document.getElementById("showall-"+vid[1]).style.display="none";
				atable_hidecol("dtblatable"+vid[1],colshowhide[vid[1]],vid[1]);
			}
		});
	};

	function atable_showless(me){
		xhr.abort();
		var vid = me.id.split("-");
		var v_afind = $("#txtfind-"+vid[1]).val();
		document.getElementById("atablepreloader"+vid[1]).style.display="flex";

		var tbpage = Object.assign({}, datapost);
		numpage[vid[1]]=1;
		tbpage["atabledata"+vid[1]]=true;
		tbpage["sortby"]=sortby[vid[1]];
		tbpage["colshowhide"]=colshowhide[vid[1]];
		tbpage["fromatable"]=true;
		tbpage.afind=v_afind;
		xhr = $.ajax({
			type: "POST",
			url: thepage,
			data: tbpage,
			success: function(data){
				document.getElementById("atablepreloader"+vid[1]).style.display="none";
				var atableno=[];
				var htmldata = "<div>"+rbline(data)+"</div>";
				$(htmldata).find(".dtatable").each(function(i, obj){
					atableno[i]=this.innerHTML;
				});

				forEach.call(atable, function (el, i) {
					if(i==vid[1]){
						atable[i].innerHTML=atableno[i];
					}
				});
				document.getElementById("showless-"+vid[1]).style.display="none";
				document.getElementById("showall-"+vid[1]).style.display="inline-block";
				atable_hidecol("dtblatable"+vid[1],colshowhide[vid[1]],vid[1]);
			}
		});
	};

	function atable_sortedby(me){
		xhr.abort();
		var vid = me.id.split("-");
		var v_afind = $("#txtfind-"+vid[1]).val();
		document.getElementById("atablepreloader"+vid[1]).style.display="flex";
		if(ascdsc[vid[1]]==""){
			sortby[vid[1]] = vid[2]+" ASC";
			ascdsc[vid[1]]="ASC";
		}else if(ascdsc[vid[1]]=="ASC"){
			sortby[vid[1]] = vid[2]+" DESC";
			ascdsc[vid[1]]="DESC";
		}else{
			sortby[vid[1]] = vid[2]+" ASC";
			ascdsc[vid[1]]="ASC";
		}

		var tbpage = Object.assign({}, datapost);
		tbpage["atabledata"+vid[1]]=true;
		tbpage["sortby"]=sortby[vid[1]];
		tbpage["colshowhide"]=colshowhide[vid[1]];
		tbpage["fromatable"]=true;
		tbpage.afind=v_afind;
		xhr = $.ajax({
			type: "POST",
			url: thepage,
			data: tbpage,
			success: function(data){
				document.getElementById("atablepreloader"+vid[1]).style.display="none";
				var atableno=[];
				var htmldata = "<div>"+rbline(data)+"</div>";
				$(htmldata).find(".dtatable").each(function(i, obj){
					atableno[i]=this.innerHTML;
				});

				forEach.call(atable, function (el, i) {
					if(i==vid[1]){
						atable[i].innerHTML=atableno[i];
					}
				});
				document.getElementById("showless-"+vid[1]).style.display="none";
				document.getElementById("showall-"+vid[1]).style.display="inline-block";
				atable_hidecol("dtblatable"+vid[1],colshowhide[vid[1]],vid[1]);
			}
		});
	};

	function load_atable(curpage,post){
		thepage = curpage;
		datapost=JSON.parse(post);
		if(datapost.length != 0 && post.toLowerCase().indexOf("toatable") < 0){
			datapost=[];
		}
		var loadtable = Object.assign({}, datapost);
		var atable = document.querySelectorAll(".dtatable");
		var forEach = [].forEach;
		forEach.call(atable, function (el, i) {
			numpage[i]=1;
			sortby[i]="";
			colshowhide[i]=[];
			ascdsc[i]="";
			loadtable["atabledata"+i]=true;
			document.getElementById("atablepreloader"+i).style.display="flex";
		});

		loadtable.fromatable=true;

		xhr = $.ajax({
			type: "POST",
			url: thepage,
			data: loadtable,
			success: function(data){
				var atableno=[];
				var htmldata = "<div>"+rbline(data)+"</div>";
				$(htmldata).find(".dtatable").each(function(i, obj){
					 atableno[i]=this.innerHTML;
				});

				var atable = document.querySelectorAll(".dtatable");
				var forEach = [].forEach;
				forEach.call(atable, function (el, i) {
					if(data!=""){
						atable[i].innerHTML=atableno[i];
						atablests[i]=true;
					}
					document.getElementById("atablepreloader"+i).style.display="none";
				});
			}
		});
	}

	function atable_reload(vid){
	  var myEle = document.getElementById("atablepreloader"+vid);
	  if(myEle){
		xhr.abort();
			var v_afind = $("#txtfind-"+vid).val();
			document.getElementById("atablepreloader"+vid).style.display="flex";
			document.getElementById("showless-"+vid).style.display="none";
			document.getElementById("showall-"+vid).style.display="inline-block";

			var tbpage = Object.assign({}, datapost);
			tbpage["atabledata"+vid]=true;
			tbpage["sortby"]=sortby[vid];
			tbpage["colshowhide"]=colshowhide[vid[1]];
			tbpage["fromatable"]=true;
			tbpage.afind=v_afind;

			xhr = $.ajax({
				type: "POST",
				url: thepage,
				data: tbpage,
				success: function(data){
					document.getElementById("atablepreloader"+vid).style.display="none";
					var atableno=[];
					var htmldata = "<div>"+rbline(data)+"</div>";
					$(htmldata).find(".dtatable").each(function(i, obj){
						atableno[i]=this.innerHTML;
					});

					forEach.call(atable, function (el, i) {
						if(i==vid){
							atable[i].innerHTML=atableno[i];
						}
					});
				}
			});
		}else{
			console.log("aTable "+vid+" not Exist.");
	  }
	}

	function atable_toxls(tableID, filename = ""){
		var downloadLink;
		var dataType = "application/vnd.ms-excel";
		var tableSelect = document.getElementById(tableID);
		var tableHTML = remHiddenTag(tableSelect.outerHTML,"none").replace(/ /g,"%20").replace(/<\/?a[^>]*>/g,"").replace(\'border="0"\',\'border="1"\');
		filename = filename?filename+".xls":"excel_data.xls";
		downloadLink = document.createElement("a");

		document.body.appendChild(downloadLink);

		if(navigator.msSaveOrOpenBlob){
			var blob = new Blob(["\ufeff", tableHTML], {
			type: dataType
			});
			navigator.msSaveOrOpenBlob(blob, filename);
		}else{
			downloadLink.href = "data:"+dataType+", "+tableHTML;
			downloadLink.onclick = atabledestroyClickedElement;
			downloadLink.download = filename;
			downloadLink.click();
		}
	}
	function atabledestroyClickedElement(event){document.body.removeChild(event.target);}

	function atable_hidecol(tblid,arcol,atablenum="") {
		var cols = arcol;
		if(cols.length < 0){
			console.log("Invalid");
			return;
		}else{
			var tbl = document.getElementById(tblid);
			var slctmltp = document.getElementById("slctmltp"+atablenum);
			if (tbl != null) {
				for (var i = 0; i < tbl.rows.length; i++) {
					var ncc=0;
					if(tbl.rows[i].cells.length>1){
						for (var j = 0; j < tbl.rows[i].cells.length; j++) {
							tbl.rows[i].cells[j].style.display = "";
							colspan = tbl.rows[i].cells[j].getAttribute("colspan");
							if(colspan>0){
								 ncc = ncc+parseInt(colspan)-1;
							}
							slctmltp.options[ncc].selected = true;
							if(cols.includes(ncc)){
								tbl.rows[i].cells[j].style.display = "none";
		 						slctmltp.options[ncc].selected = false;
							}
							ncc++;
						}
					}
				}
			}
		}
		colshowhide[atablenum]=arcol;
	}

	function getSelectMultiValues(select) {
		var result = [];
		var options = document.getElementById(select);
		for (var i=0, iLen=options.length; i<iLen; i++) {
			if (!options[i].selected) {result.push(parseInt(options[i].value) || parseInt(options[i].text) || 0);}
		}
		return result;
	}
	function atable_showhide(meid=""){
		var tag = document.getElementById(meid);
		if(tag.style.display === "block"){
			tag.style.display = "none";
		}else{
			tag.style.display = "block";
		}
	}
	function remHiddenTag(html, match) {
	    var container = document.createElement("span");
	    container.innerHTML = html;
	    Array.from(container.querySelectorAll("[style*="+CSS.escape(match)+"]"))
	        .forEach( link => link.parentNode.removeChild(link));
	    return container.innerHTML;
	}
	function rbline(str){var text=str;text = text.replace(/(\r\n|\n|\r)/gm," ");text = text.replace(/\s{2,}/g, " ");return text;}


	function atable_processdata(ntbl,me,prc,cols,colsv,numb){
	  var rows=[];var nm=0;
		if(numb){nm=1;}
	  $(me).parents("tr").each(function(i) {
	    $("td", this).each(function(j){rows.push($(this).html().replace("&nbsp;","").replace(/&amp;/g, "&")
      .replace(/&lt;/g, "<")
      .replace(/&gt;/g, ">")
      .replace(/&quot;/g, "\"")
      .replace(/&#039;/g, "\'"));});
	  });

	  var frm=document.getElementById("atform"+ntbl);
	  frm.style.display="block";
	  frm.innerHTML="";
	  if(prc=="add"){
	    rows=colsv;nm=0;
	  }

	  var dv = document.createElement("div");
	  var table=document.createElement("table");
	  table.setAttribute("class", "atble");
	  table.setAttribute("id", "atble"+ntbl);
	  var rowCount=table.rows.length;var ni=0;var idsetf="";
		for(var i=0;i<(rows.length-1)-nm;i++){
			if(!cols[i].includes(";")){
		    var row=table.insertRow(ni);
		    var sp = document.createElement("span");
		    sp.innerHTML=colsv[i];

		    var inp = document.createElement("input");
		    inp.setAttribute("type", "text");
		    inp.setAttribute("id", cols[i]+"-"+ntbl);
		    inp.setAttribute("class", "form-control");
		    if(prc=="delete"){
		      inp.setAttribute("readonly", "readonly");
		      inp.setAttribute("style", "margin-bottom:5px;background:#ffffff");
		    }else{
		      inp.setAttribute("style", "margin-bottom:5px;");
		    }

		    if(prc!="add"){inp.value=rows[i+nm];}
				if(ni==0){idsetf=cols[i]+"-"+ntbl;}

		    var newcell=row.insertCell(0);
		    newcell.appendChild(sp);
		    newcell=row.insertCell(1);
		    newcell.innerHTML="&nbsp;&nbsp;&nbsp;";
		    newcell=row.insertCell(2);
		    newcell.appendChild(inp);
				ni++;
			}
	  }

	  var cn = document.createElement("button");
	  cn.setAttribute("type", "button");
	  cn.setAttribute("class", "btn btn-default btn-xs");
	  cn.setAttribute("style", "font-size:18px;height:30px;float:right;");
	  cn.innerHTML="<span class=\"ic cross\"></span>";
	  var sv = document.createElement("button");
	  sv.setAttribute("type", "button");
	  sv.setAttribute("style", "font-size:18px;height:30px;float:right;margin-right:5px;");
	  if(prc=="delete"){
	    sv.setAttribute("class", "btn btn-danger btn-xs");
	    sv.innerHTML="<span class=\"ic trash-white\"></span>";
	  }else{
	    sv.setAttribute("class", "btn btn-info btn-xs");
	    sv.innerHTML="<span class=\"ic save-white\"></span>";
	  }
	  $(cn).on("click",function(e){frm.style.display="none";});
	  $(sv).on("click",function(e){
	    var vdata={};var ndata={};

	    for (var i = 0; i < (rows.length-1)-nm; i++) {
				if(!cols[i].includes(";")){
		      vdata[cols[i]]=$("#"+cols[i]+"-"+ntbl).val();
		      ndata[cols[i]]=rows[i+nm].replace("&nbsp;","");
				}
	    }

	    $.post(thepage,{process_table:ntbl,vdata:vdata, ndata:ndata, atable_process_data:prc},function(data){//console.log(data);
	      if(data.includes("atable_process_true")){
	        if(prc=="delete" || prc=="add"){frm.style.display="none";}
	        if(prc!="add"){
		        for (var i = 0; i < (rows.length-1)-nm; i++) {
							if(!cols[i].includes(";")){
			          rows[i+nm]=$("#"+cols[i]+"-"+ntbl).val();
							}
		        }
	          atable_topage(ntbl,atable_getpage(ntbl));
	        }else{
	          atable_reload(ntbl);
	        }
	      }else{
					console.log("Process Failed.")
				}
	    });
	  });

	  if(prc=="delete"){
	    dv.innerHTML=\'<h4>Delete Data?</h4><hr style="margin-top: 5px;margin-bottom: 10px;">\';
	  }else if(prc=="add"){
	    dv.innerHTML=\'<h4>Add Data</h4><hr style="margin-top: 5px;margin-bottom: 10px;">\';
	  }else{
	    dv.innerHTML=\'<h4>Edit Data</h4><hr style="margin-top: 5px;margin-bottom: 10px;">\';
	  }
	  dv.appendChild(table);
	  dv.appendChild(cn);
	  dv.appendChild(sv);
	  frm.appendChild(dv);
		if(prc!="delete"){$("#"+idsetf).focus();}
	}
	</script>';
	$http_s = isset($_SERVER['HTTPS'])?"https://":"http://";
	$this_page = $http_s.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	echo "<script>$(document).ready(function(e) {load_atable('".$this_page."','".json_encode($_POST)."');});</script>";
	}
}
?>
