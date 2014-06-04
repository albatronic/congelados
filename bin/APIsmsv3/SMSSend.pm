###########################################################################
## Título: SMSSend.pm
## Descripción: Funciones para enviar SMS desde PERL
###########################################################################

package Arsys::SMSSend;
use Exporter ();
@ISA = qw(Exporter);
@EXPORT = qw(
	periodUnica
	periodDiaria 
	periodSemanal
	periodMensual
	periodAnual
   );
	use LWP::Simple;	
	use LWP::UserAgent;

	sub periodUnica { 1 }
	sub periodDiaria { 2 }
	sub periodSemanal { 3 }
	sub periodMensual { 4 }
	sub periodAnual { 5 }
	sub host {'http://sms.arsys.es:8080' }
	sub sSend {'/smsarsys/accion/enviarSms2.jsp' }
	sub sProgram {'/smsarsys/accion/ProgramarSmsEP.jsp' }

	sub ObtenURL ($;$)
	{
		my($url) = shift;
		my($parametros) = shift;

		$ua = new LWP::UserAgent;
		$request = new HTTP::Request('POST', $url);
	   $request->content_type('application/x-www-form-urlencoded');
	   $request->content($parametros);

		$response = $ua->request($request);
		return $response->content();
	}


	sub URLEncode
	{
	    my($url)=@_;
	    my(@characters)=split(/(\%[0-9a-fA-F]{2})/,$url);
	
	    foreach(@characters)
	    {
		if ( /\%[0-9a-fA-F]{2}/ ) # Escaped character set ...
		{
		    # IF it is in the range of 0x00-0x20 or 0x7f-0xff
		    #    or it is one of  "<", ">", """, "#", "%",
		    #                     ";", "/", "?", ":", "@", "=" or "&"
		    # THEN preserve its encoding
		    #"
		    unless ( /(20|7f|[0189a-fA-F][0-9a-fA-F])/i
			    || /2[2356fF]|3[a-fA-F]|40/i )
		    {
			s/\%([2-7][0-9a-fA-F])/sprintf "%c",hex($1)/e;
		    }
		}
		else # Other stuff
		{
		    # 0x00-0x20, 0x7f-0xff, <, >, and " ... "
		    s/([\000-\040\177-\377\074\076\042])
		     /sprintf "%%%02x",unpack("C",$1)/egx;
		}
	    }
	    return join("",@characters);
	}

	
	#Creación de un objeto SMS
	sub new {
		my ($pkg) = shift;
		bless {
			id => '', psw => '', phoneNumber => '', textSms => '' , desripcionEP => '', date => '', time => '', period => ''
		}, $pkg;
	}
	
	# Se recuperan todos los posibles valores
	sub getAccount {my $obj = shift; $obj->{id}}
	sub setAccount {my $obj = shift; $obj->{id} = shift }

	sub getPwd {my $obj = shift; $obj->{psw}}
	sub setPwd {my $obj = shift; $obj->{psw} = shift }

	sub getTo {my $obj = shift; $obj->{phoneNumber}}
	sub setTo {my $obj = shift; $obj->{phoneNumber} = shift }

	sub getText {my $obj = shift; $obj->{textSms}}
	sub setText {my $obj = shift; $obj->{textSms} = URLEncode(shift)}

	sub getFrom {my $obj = shift; $obj->{remite}}
	sub setFrom {my $obj = shift; $obj->{remite} = URLEncode(shift);}

	# Parámetros para programar un mensaje por la API
	sub getDescriptionEP {my $obj = shift; $obj->{descripcion}}
	sub setDescriptionEP {my $obj = shift; $obj->{descripcion} = URLEncode(shift) }

	sub getDateEP {my $obj = shift; $obj->{fecha}}
	sub setDateEP {my $obj = shift; $obj->{fecha} = shift }
	
	sub getTimeEP {my $obj = shift; $obj->{hora}}
	sub setTimeEP {my $obj = shift; $obj->{hora} = shift }

	sub getPeriodEP {my $obj = shift; $obj->{periodicidadEnvio}}
	sub setPeriodEP {my $obj = shift; $obj->{periodicidadEnvio} = shift }

	# Resultado
	sub getResult {my $obj = shift; $obj->{smsResult}}
	sub getDescription {my $obj = shift; $obj->{smsDescription}}	
	sub getCredit {my $obj = shift; $obj->{smsCredit}}

	sub Send
	{
		my $obj = shift;
		my $URLSMS = host() . sSend();
		my $param .= join "&", map "$_=$obj->{$_}",keys %$obj;

	  	my $resultado = ObtenURL ($URLSMS,$param);
		my $smsResult;
		my $smsDescription;
		my $smsCredit;

     	$resultado=~ /<result>(.*)<\/result>/is;
     	$smsResult=$1;
     	$resultado=~ /<description>(.*)<\/description>/is;
     	$smsDescription=$1;
     	$resultado=~ /<credit>(.*)<\/credit>/is;
     	$smsCredit=$1;

     	if ((!$smsResult) || (!$smsDescription))
     	{
        $resultado = "-1";
        $obj->{smsResult} = "KO";
        $obj->{smsDescription} = "Error de conexión al servidor SMS";
        $obj->{smsCredit} = "";
     	}
     	else
     	{
        $obj->{smsResult} = $smsResult;
        $obj->{smsDescription} = $smsDescription;
        $obj->{smsCredit} = $smsCredit;
     	}  		
		return $resultado;
    }

	sub Program
	{
		my $obj = shift;
		my $URLSMS = host() . sProgram();
		my $param .= join "&", map "$_=$obj->{$_}",keys %$obj;
	  	my $resultado = ObtenURL ($URLSMS,$param);
		my $smsResult;
		my $smsDescription;
		my $smsCredit;

      $resultado=~ /<result>(.*)<\/result>/is;
      $smsResult=$1;
      $resultado=~ /<description>(.*)<\/description>/is;
      $smsDescription=$1;
     	$resultado=~ /<credit>(.*)<\/credit>/is;
     	$smsCredit=$1;

     	if ((!$smsResult) || (!$smsDescription))
     	{
        $resultado = "-1";
        $obj->{smsResult} = "KO";
        $obj->{smsDescription} = "Error de conexión al servidor SMS";
     	}
     	else
     	{
        $obj->{smsResult} = $smsResult;
        $obj->{smsDescription} = $smsDescription;
        $obj->{smsCredit} = $smsCredit;
     	}
        
		return $resultado;
    }	 
	 	
1;

