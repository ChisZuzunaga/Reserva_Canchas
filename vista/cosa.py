Nombre = input("Ingrese nombre del estudiante: ")
Asistencia_total = float(input("Ingrese la asistencia total del estudiante: "))
Asistencia_real = float(input("Ingrese la asistencia real del estudiante: "))
Porcentaje_asistencia = (Asistencia_real / Asistencia_total) * 100
print("Estimado",Nombre+", su asistencia es de un",str(Porcentaje_asistencia)+"%")