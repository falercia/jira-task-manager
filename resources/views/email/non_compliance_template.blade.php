<html><table style="width: 800px;background-color: #1d5679; margin: auto">
      <tr>
         <td  colspan="2" style="color: #f7f7f7;font-family: Arial;line-height: 25px;padding-left: 30px;padding-right: 30px;">      
            <p style="padding-left: 20px;padding-right: 15px;font-size: 17px;padding-top: 30px;padding-bottom: 10px;border-top: 1px #d1d1d1 solid;">
               Olá {{ $nonCompliance->name }}! Foi inserida uma não conformidade para o seu usuário. Qualquer justificativa, sugestão ou critica, pode ser feita via
               e-mail (por enquanto) para <a href="#" style="text-decoration: none;color:#6fcedf!important;"><b style="color:#6fcedf!important;font-weight: 200;">fabio.garcia@hubchain.com</b></a> com texto puro de até 1500 caracteres e mencionando o titulo do e-mail enviado a você.
               Segue a não confirmidade:
            </p>
         </td>
      </tr>
      <tr>
         <td style="color: #f7f7f7;font-family: Arial;line-height: 25px;padding-left: 30px;padding-right: 30px;padding-top:15px;padding-bottom:15px;">
            <p style="color:#f7f7f7;font-size: 17px;padding-right: 30px;padding-left: 20px;"><b>DATA:</b> <span style="color:#ffffff;">{{ \Carbon\Carbon::parse($nonCompliance->date)->format('d/m/Y') }}</span></p>
            <p style="color:#f7f7f7;font-size: 17px;padding-right: 30px;padding-left: 20px;"><b>SEVERIDADE:</b> <span style="color:{{ $nonCompliance->severity_color }}">{{ $nonCompliance->severity_description }}</span></p> 
            <p style="color:#f7f7f7;font-size: 17px;padding-right: 30px;padding-left: 20px;padding-bottom: 20px;"><b>TIPO:</b> {{ $nonCompliance->type_description }}</p> 
         </td>
      </tr>
      <tr>
         <td colspan="2" style="color: #f7f7f7;font-family: Arial;line-height: 25px;padding-left: 30px;padding-right: 30px;">
            <p style="display:block;color: #6fcedf;font-family: Arial;line-height: 25px;padding-left: 17px;">DESCRIÇÃO</p>
            <p style="font-size: 14px;padding-right: 30px;padding-left: 20px;padding-bottom: 20px;">{{ $nonCompliance->description }}.</p>
            <p style="display:block;color: #6fcedf;font-family: Arial;line-height: 25px;padding-left: 17px;">IMPACTO</p>
            <p style="font-size: 14px;padding-right: 30px;padding-left: 20px;padding-bottom: 20px;">{{ $nonCompliance->impact }}.</p>
            <p style="color: #6fcedf;font-family: Arial;line-height: 25px;padding-left: 17px;">O QUE DEVE SER FEITO?</p>
            <p style="font-size: 14px;background-color: #1d5679;padding-right: 30px;padding-left: 20px;padding-bottom: 20px;">{{ $nonCompliance->action_plan }}.</p>
         </td>
      </tr>
      <tr>
         <td colspan="2" colspan="2" style="color: #f7f7f7;font-family: Arial;line-height: 25px;padding-left: 30px;padding-right: 30px;">      
            <p style="text-align:left;padding-left: 20px;padding-right: 15px;font-size: 14px;padding-top: 30px;">
               Atenciosamente,
            </p>
            <p style="text-align:left;padding-left: 20px;padding-right: 15px;font-size: 17px;padding-bottom: 10px;">
               Hubchain Team
            </p>  
         </td>
      </tr>
      <tr>
         <td colspan="2" colspan="2" style="color: #f7f7f7;font-family: Arial;line-height: 25px;padding-left: 30px;padding-right: 30px;">      
            <p style="text-align:center;padding-left: 20px;padding-right: 15px;font-size: 17px;padding-top: 30px;padding-bottom: 10px;border-top: 1px #d1d1d1 solid;">
               Hubchain {{ now()->year }}
            </p>  
         </td>
      </tr>
   </table>
</html>