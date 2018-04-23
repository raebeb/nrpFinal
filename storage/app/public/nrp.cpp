#include <iostream>
#include <fstream>
#include <sstream>
#include <algorithm>
#include <string>
#include <cstdlib>
#include <vector>
#include <map>
#include <unistd.h>
#include <dirent.h>
#include <sys/stat.h>
#include <omp.h>

using namespace std;

typedef vector<string> strVect;
typedef vector<int> intVect;
typedef vector<bool> boolVect;
typedef map<int, intVect> intIntVectMap;

struct Employees {                          //Estructura de los funcionarios.
    string name;                            //Nombre del funcionario.
    string lastname;                        //Apellido del funcionario.
    string path;                            //Ruta del archivo personal mensual de salida del funcionario.
    int personalNumber;                     //Número personal (RUT) del funcionario.
    int shiftType;                          //Tipo de turno(s) que prefiere. Por defecto, puede ser cualquiera.
    int priority;                           //Prioridad del funcionario para tomar turnos.
    int freeDaysToSchedule = 0;             //Contador de días libres por agendar.
    int freeDaysCounter = 0;                //Contador de días libres que lleva el funcionario.
    int holidays[2] = {-1,-1};              //Día de inicio y término de sus vacaciones.
    int blocked[2] = {-1,-1};               //Día de inicio y término de un bloqueo de servicios del funcionario.
    int weekExtraWorked = 0;                //Semanas que ha trabajado un día extra.
    int hours;                              //Cantidad de horas trabajadas
    bool workingDays[7];                    //Días de la semana en que trabaja (boolean).
    bool extraWork = 0;                     //Flag para saber si ha trabajo extra.
    bool pregnant = 0;                      //Booleando para saber si la funcionario se encuentra embarazada.
    intVect freeDays;                       //Días del mes que funcionario solicitó libre.
    intVect disability;                     //Días de inicio y término de discapacidad por licencia.
    intVect deceased;                       //Día de inicio y término de días libres por fallecimiento de familiar.
    intVect extraDays;                      //Días que ha trabajo extra.
    intVect criticalDays;                   //Días críticos trabajados previamente.
    intVect LargeColombia;
    strVect weekPath;                       //Rutas de los archivos personales semanales de salida del funcionario.
};

struct Settings {                           //Estructura de los turnos.
    string name;                            //Nombre del turno.
    int length;                             //Duración del turno en horas.
    int maxConsecutive;                     //Número de veces que se puede asignar este turno consecutivamente.
    int employeesRequired[7];               //Cantidad de funcionarios requeridos para cada día de la semana.
    intVect invalidNextShift;               //Turnos que no le pueden seguir a este.
};

struct AdditionalInfo {
    int maxHoursPerDay;                     //Margen legal de máxima de horas laburables por día.
    int maxHoursPerWeek;                    //Margen legal de máximo de horas laburables por semana.
    string serviceName;                     //BORRAR LUEGO
    string country;                         //País donde se aplica esta planificación.
    string monthPath;                       //Ruta del archivo de salida mensual.
    strVect weekPath;                       //Rutas de los archivos de salido semanales.
    intIntVectMap shiftCombinations;        //Combinaciones de turnos válidas.
    intIntVectMap totalHolidays;            //Feriados a considerar para planificar.
};

typedef map<int, Settings> shiftMap;
typedef map<unsigned int, Employees> employeeMap;

/*                                                        Cabeceras de funciones                                                */
void frontEndInformation(int hour, int minute, int month, int year, string filepath, employeeMap &employee);
void monthlyPrinter(string filename, employeeMap &employees, intIntVectMap &personalSchedule, intIntVectMap &dayMap, shiftMap &shift);
void weeklyPrinter(string filename, employeeMap &employee, shiftMap &shift, intIntVectMap &personalSchedule, intIntVectMap &dayMap);
void weeklyPersonalPrinter(string filename, employeeMap &employee, shiftMap &shift, intIntVectMap &personalSchedule, intIntVectMap &dayMap);
void personalPrinter(string filename, intIntVectMap &personalSchedule, employeeMap &employee, int firstDay, int vectLength,shiftMap &shift);
void readCriticalMonth(int month, int year, string filepath, employeeMap &employee, shiftMap &shift);
void readPreviousSchedules(int thisMonth, int thisYear, string filepath, intIntVectMap &previousSchedule, shiftMap &shifts, employeeMap &employee);
void typeFree(intIntVectMap &personalSchedule,int employeeID, struct Employees &employeeData, int day);
void scheduler(int day, int month, int year, int hour, int minute, string filepath, shiftMap &shift, employeeMap &employees);
void addShift(intIntVectMap &personalSchedule, intVect &shift, int employeeID, intIntVectMap &dayMap, int day, int shiftID, int daysInMonth, struct Employees &employeeData);
void readSettings(const string &filename, shiftMap &shift);
void multipleAppend(string &filename, strVect toAppend);
void giveMeMyFreeDays(int &freeDaysToSchedule,int &freeDaysCounter,int day,int month);
void readEmployees(int month, int year, const string &filename, employeeMap &employee, shiftMap &shift);
void readHolidays(const string filename, int month, int year, employeeMap &employee);
int checkMyBallsForLungCancer(struct Employees &employeeData, intIntVectMap &personalSchedule, int currentDay, int employeeID, bool flag = true);
int split(ifstream &input, strVect &result, const char *str, char c = ',', int lower = 0, int higher = 100);
int formatErrorHandler(ifstream &input, const string &buffer, int lower = 1, int higher = 999999999);
int daysInCurrentMonth(int month, int year);
int firstDayOfMonth(int month, int year);
int highestID(employeeMap &employees);
int dayOfWeek(const string &weekDay);
string moveFilePointer(ifstream &input, int linesToMove, int posCheck = 0, const string &toCheck = string(), bool flag = false);
string dayOfWeek(int weekDay);
string currentMonth(int month);
string currentTime(int hour, int minute);
string getDirectory(string path, string limit = string(), string replace = string());
string typeSchedule(int shiftID, shiftMap &shift, intVect &extraDays, int day);
string getLatestPath(string filepath, string period, int daysInMonth);
bool otherShifts(int shiftID, int shiftType, shiftMap &shift, int day, intIntVectMap &dayMap, int daysInMonth);
bool canSchedule(struct Settings &shiftData, intIntVectMap &personalSchedule, int employeeID, int dayNumber);
bool isWorkingDay(struct Employees &employeeData, int day, int dayNumber, int shiftID);
bool weekMax(intIntVectMap &personalSchedule, intIntVectMap &previousSchedule, intIntVectMap &dayMap, int employeeID, int dayNumber, shiftMap &shift);
bool consecutiveDays(intIntVectMap &personalSchedule, int employeeID, int shiftID,shiftMap &shift);
bool canExploit(int explotedPriority, int day, int explotedID , int employeeID, intIntVectMap &personalSchedule, employeeMap &employees);
bool previousFreeDays(int employeeID, intIntVectMap &previousSchedule);
bool nextShift(int employeeID, int shiftType, intIntVectMap &previousSchedule, intIntVectMap &personalSchedule, intIntVectMap &dayMap, struct Employee &employeeData);
bool restCondition(int Rep,int employeeID,intVect &thoseGuysNeedRest);
bool strToInt(string &toInt, int &value);
intIntVectMap schedulerGeneretor(shiftMap &shift, int daysInMonth);
intIntVectMap assingShift(employeeMap &employees, shiftMap &shift, intIntVectMap &dayMap, intIntVectMap &previousSchedule, int daysInMonth, int month);

//Variables Globales//
struct AdditionalInfo info;
intIntVectMap Volantes;

int main(int argc, char *argv[]) {
    time_t t = time(NULL);
    tm *timePtr = localtime(&t);
    int minute = timePtr->tm_min, hour = timePtr->tm_hour, month = timePtr->tm_mon+1, year = timePtr->tm_year+1900;
    string filepath = getDirectory(argv[2],"input");
    shiftMap nurseShifts;
    employeeMap nurses;
    intIntVectMap nurseSchedule;
    readSettings(argv[1],nurseShifts);
    readEmployees(month,year,argv[2],nurses,nurseShifts);
    readHolidays(argv[3],month,year,nurses);
    if(!info.totalHolidays[month].empty())
        readCriticalMonth(month,year,filepath,nurses,nurseShifts);
    scheduler(timePtr->tm_mday,month,year,hour,minute,filepath,nurseShifts,nurses);
    frontEndInformation(hour,minute,month,year,filepath,nurses);
    cout << endl << "Archivos creados con éxito" << endl << endl;
    return /*atoi(currentTime(hour,minute).c_str())*/0;
}

void frontEndInformation(int hour, int minute, int month, int year, string filepath, employeeMap &employee) {
    int vectLength = info.weekPath.size();
    string filename, auxFilename;
    ofstream output;
    multipleAppend(filepath,{"output/",currentTime(hour,minute)});
    filename = filepath;
    filename.append("/general.csv");
    output.open(filename);
    if(output.is_open()){
        filename = string();
        multipleAppend(filename,{currentMonth(month),to_string(year)});
        auxFilename = filename;
        auxFilename.append(".csv");
        output << auxFilename << ";" << info.monthPath << endl;
        filename.append("Semana ");
        for(int i = 0; i < vectLength; i++){
            auxFilename = filename;
            multipleAppend(auxFilename,{to_string(i+1),".csv"});
            output << auxFilename << ";" << info.weekPath[i] << endl;
        }
        output.close();
    }
    filename = filepath;
    filename.append("/personal.csv");
    output.open(filename);
    if(output.is_open()){
        for(employeeMap::iterator it = employee.begin(); it != employee.end(); ++it){
            filename = string();
            multipleAppend(filename,{to_string(it->first),".csv"});
            output << it->first << ";" << it->second.name << ";" << it->second.lastname << ";" << filename << ";" << it->second.path;
            filename = "Semana ";
            for(int i = 0; i < vectLength; i++){
                auxFilename = filename;
                multipleAppend(auxFilename,{to_string(i+1),".csv"});
                output << ";" << auxFilename << ";" << it->second.weekPath[i];
            }
            output << endl;
        }
        output.close();
    }
}

void personalPrinter(string filename, intIntVectMap &personalSchedule, employeeMap &employee, int firstDay, int vectLength, shiftMap &shift) {
    filename.append("individual/");
    mkdir(filename.c_str(),S_IRWXU | S_IRWXG | S_IROTH | S_IXOTH);
    for(employeeMap::iterator it = employee.begin(); it != employee.end(); ++it){
        int auxDay = 1;
        string strOut = string(filename);
        ofstream output;
        multipleAppend(strOut,{to_string(it->first),"/"});
        mkdir(strOut.c_str(),S_IRWXU | S_IRWXG | S_IROTH | S_IXOTH);
        multipleAppend(strOut,{to_string(it->first),".csv"});
        employee[it->first].path = strOut;
        output.open(strOut.c_str());
        output << "sep=;" << endl << "EmpleadoID;Nombre;Apellido" << endl << it->first << ";" << it->second.name << ";" << it->second.lastname << endl << endl;
        output << "# HORARIO INDIVIDUAL: Dia[Turno]" << endl << "LUN;MAR;MIE;JUE;VIE;SAB;DOM" << endl;
        while(auxDay != firstDay){
            output << ";";
            auxDay += 1;
            if(auxDay == 7)
                auxDay = 0;
        }
        auxDay = firstDay - 1;
        for(int i = 0; i < vectLength; i++){
            output << i+1 << "[" << typeSchedule(personalSchedule[it->first][i],shift,it->second.extraDays,i) << "];";
            auxDay += 1;
            if(auxDay == 7){
                auxDay = 0;
                output << endl;
            }
        }
        output.close();
    }
}

void weeklyPersonalPrinter(string filename, employeeMap &employee, shiftMap &shift, intIntVectMap &personalSchedule, intIntVectMap &dayMap) {
    int daysInMonth = dayMap.size(), numberOfWeeks = 0, shiftSize = shift.size();
    ofstream output;
    filename.append("individual/");
    for(int i = 1; i <= daysInMonth; i++)
        if(dayMap[i][0] == 0 || (i == daysInMonth && dayMap[i][0] != 0))
            numberOfWeeks += 1;
    for(employeeMap::iterator it = employee.begin(); it != employee.end(); ++it){
        int currentDay = 1;
        for(int i = 1; i <= numberOfWeeks; i++){
            int auxDay = currentDay, daysInWeek, auxDay3;
            bool flag = true;
            string auxFilename = filename, weekNumber = to_string(i);
            multipleAppend(auxFilename,{to_string(it->first),"/Semana ",to_string(i),".csv"});
            employee[it->first].weekPath.push_back(auxFilename);
            output.open(auxFilename);
            output << "sep=;" << endl << "EmpleadoID;Nombre;Apellido" << endl << it->first << ";" << it->second.name << ";" << it->second.lastname << endl << endl;
            output << "# HORARIO INDIVIDUAL: Dia[Turno]" << endl << "LUN;MAR;MIE;JUE;VIE;SAB;DOM" << endl << endl;
            output << "# Horario Semana " << weekNumber << endl << endl << ";;";
            for(int auxDay2 = dayMap[currentDay][0]; flag == true; auxDay2++, currentDay++){
                if(currentDay > daysInMonth)
                    break;
                else if(auxDay2 == 0 || auxDay2 > 6)
                    flag = false;
                output << currentDay << ";";
            }
            if(currentDay > daysInMonth)
                currentDay = daysInMonth;
            output << endl << "ID;NOMBRE;";
            for(int auxDay2 = auxDay; auxDay2 < currentDay; auxDay2++){
                output << dayOfWeek(dayMap[auxDay2][0]) << ";";
                if(auxDay2 + 1 == daysInMonth)
                    output << dayOfWeek(dayMap[daysInMonth][0]) << ";";
            }
            daysInWeek = currentDay - auxDay;
            auxDay3 = currentDay - 1;
            output << endl << it->first << ";" << employee[it->first].name;
            for(int auxDay2 = auxDay - 1, i = 0; auxDay2 < auxDay3; auxDay2++, i++){
                output << ";" << typeSchedule(personalSchedule[it->first][auxDay2],shift,it->second.extraDays,auxDay2);
                if(auxDay2 + 2 == daysInMonth)
                    output << ";" << typeSchedule(personalSchedule[it->first][daysInMonth-1],shift,it->second.extraDays,daysInMonth);
            }
            output.close();
        }
    }
}

void weeklyPrinter(string filename, employeeMap &employee, shiftMap &shift, intIntVectMap &personalSchedule, intIntVectMap &dayMap) {
    int daysInMonth = dayMap.size(), numberOfWeeks = 0, currentDay = 1, shiftSize = shift.size();
    string auxFilename;
    ofstream output;
    filename.append(" Semana ");
    for(int i = 1; i <= daysInMonth; i++)
        if(dayMap[i][0] == 0 || (i == daysInMonth && dayMap[i][0] != 0))
            numberOfWeeks += 1;
    for(int i = 1; i <= numberOfWeeks; i++){
        int auxDay = currentDay, daysInWeek, auxDay3;
        bool flag = true;
        intIntVectMap counter;
        string weekNumber = to_string(i), auxFilename = filename;
        multipleAppend(auxFilename,{weekNumber,".csv"});
        info.weekPath.push_back(auxFilename);
        output.open(auxFilename);
        output << "sep=;" << endl << "# Horario Semana " << weekNumber << endl << endl << ";;";
        for(int auxDay2 = dayMap[currentDay][0]; flag == true; auxDay2++, currentDay++){
            if(currentDay > daysInMonth)
                break;
            else if(auxDay2 == 0 || auxDay2 > 6)
                flag = false;
            output << currentDay << ";";
        }
        if(currentDay > daysInMonth)
            currentDay = daysInMonth;
        output << endl << "ID;NOMBRE;";
        for(int auxDay2 = auxDay; auxDay2 < currentDay; auxDay2++){
            output << dayOfWeek(dayMap[auxDay2][0]) << ";";
            if(auxDay2 + 1 == daysInMonth)
                output << dayOfWeek(dayMap[daysInMonth][0]) << ";";
        }
        daysInWeek = currentDay - auxDay;
        for(int i = -5; i <= shiftSize; i++)
            for(int j = 0; j < daysInWeek; j++)
                counter[i].push_back(0);
        auxDay3 = currentDay - 1;
        for(intIntVectMap::iterator it = personalSchedule.begin(); it != personalSchedule.end(); ++it){
            output << endl << it->first << ";" << employee[it->first].name << ";" << employee[it->first].lastname;
            for(int auxDay2 = auxDay - 1, i = 0; auxDay2 < auxDay3; auxDay2++, i++){
                output << ";" << typeSchedule(it->second[auxDay2],shift,employee[it->first].extraDays,auxDay2);
                if(auxDay2 + 2 == daysInMonth){
                    output << ";" << typeSchedule(it->second[daysInMonth-1],shift,employee[it->first].extraDays,daysInMonth);
                    counter[it->second[daysInMonth-1]][i] += 1;
                }
                counter[it->second[auxDay2]][i] += 1;
            }
        }
        output << endl;
        for(intIntVectMap::iterator it = counter.begin(); it != counter.end(); ++it){
            int vectLength = it->second.size();
            output << endl << ";" << typeSchedule(it->first,shift,it->second,0);
            for(int i = 0; i < vectLength; i++ )
                output << ";" << it->second[i];
        }
        output.close();
    }
}

void monthlyPrinter(string filename, employeeMap &employees, intIntVectMap &personalSchedule, intIntVectMap &dayMap, shiftMap &shift) {
    int workedDays=0, freeDays=0, auxSize = shift.size(), vectLength = dayMap.size();
    intIntVectMap counter;
    ofstream outputArchive;
    filename.append(".csv");
    info.monthPath = filename;
    outputArchive.open(filename);
    for(int i = -5; i <= auxSize; i++)
        for(int j = 0; j < vectLength; j++)
            counter[i].push_back(0);
    outputArchive << "sep=;" << endl << "# Esto es un comentario. Comentarios comienzan con #\n# Dias de la semana = LUN, MAR, MIE, JUE, VIE, SAB, o DOM\n\n";
    outputArchive << "LISTADO_DE_ENFERMERAS\n# FuncionarioID, Nombre, Dias de la semana que trabaja (0 no trabajo, 1 si trabaja. Orden: lunes a domingo), Tipo de turno (Solo turno x, cualquier turno 0)\n";
    outputArchive<<"ID;Nombre;LUN;MAR;MIE;JUE;VIE;SAB;DOM;Turno"<<endl;
    for(employeeMap::iterator it = employees.begin(); it != employees.end(); ++it){
        outputArchive << it->first << ";" << it->second.name<<";";
        for(int i= 0; i < 6; i++)
            outputArchive << it->second.workingDays[i] << ";";
        outputArchive  << it->second.workingDays[6] << ";" << it->second.shiftType << endl;
    }
    outputArchive << endl;
    outputArchive << "#HORARIO MENSUAL"<<endl<<endl;
    outputArchive << " ;;";
    for(intIntVectMap::iterator it = dayMap.begin(); it != dayMap.end(); ++it)
        outputArchive << it->first << ";";
    outputArchive << endl<< "ID;Nombre;";
    for(intIntVectMap::iterator it = dayMap.begin(); it != dayMap.end(); ++it)
        outputArchive << dayOfWeek(it->second[0]) << ";";
    outputArchive <<" ;DIAS TRABAJADOS;DIAS LIBRES;DIAS EXTRA TRABAJADOS;DIAS FERIADOS TRABAJADOS;DIAS LIBRES POR ASIGNAR" <<endl;
    for(intIntVectMap::iterator it = personalSchedule.begin(); it != personalSchedule.end(); ++it){
        vectLength = it->second.size()-1;
        workedDays = 0;
        freeDays = 0;
        outputArchive << it->first << ";" << employees[it->first].name << ";" << employees[it->first].lastname  << ";";
        for(int i = 0; i < vectLength; i++){
            int type = it->second[i];
            outputArchive << typeSchedule(it->second[i],shift,employees[it->first].extraDays,i) << ";";
            counter[type][i]++;
            if(type == 0)
                freeDays++;
            else if(type > 0)
                workedDays++;
        }
        if(it->second[it->second.size()-1]==0){
            freeDays++;
        }else if(it->second[it->second.size()-1] > 0){
            workedDays++;
        }
        counter[it->second[vectLength]][vectLength]++;
        outputArchive << typeSchedule(it->second[vectLength],shift,employees[it->first].extraDays,vectLength) << ";;" << workedDays-employees[it->first].weekExtraWorked  << ";" << freeDays <<";" << employees[it->first].weekExtraWorked <<";" << employees[it->first].freeDaysCounter <<";"<<  employees[it->first].freeDaysToSchedule << endl;
    }
    outputArchive << endl;
    intVect nada;
    for(intIntVectMap::iterator it = counter.begin(); it != counter.end(); ++it){
        vectLength = it->second.size();
        outputArchive << endl << ";" << typeSchedule(it->first,shift,nada,0) << ";";
        for(int i = 0; i < vectLength; i++ )
            outputArchive << it->second[i] << ";";
    }
    if(Volantes.size() == 0){
        outputArchive.close();
        return;
    }
    outputArchive << "\n\nSe requieren funcionarios volantes los siguientes dias:" << endl;
    for(intIntVectMap::iterator it = Volantes.begin();it != Volantes.end(); ++it){
        outputArchive << it->first << ";" << dayOfWeek(dayMap[it->first][0])<<";";
        for(int i=0;i<it->second.size();i++){
            outputArchive << typeSchedule(it->second[i],shift,nada,0)<<";";
        }
        outputArchive << endl;
    }
    outputArchive.close();
}

string typeSchedule(int shiftID, shiftMap &shift, intVect &extraDays, int day) {
    string extra = "E";
    if(shiftID == -5)
        return "LUTO";
    else if(shiftID == -4)
        return "AUSENTE";
    else if(shiftID == -3)
        return "SOLICITUD";
    else if(shiftID == -2) 
        return "INCAPACIDAD";
    else if(shiftID == -1)
        return "VACACION";
    else if(shiftID == 0)
        return "LIBRE";
    else
        if(extraDays.size()!=0){
            for(int i = 0; i < extraDays.size();i++){
                if(extraDays[i] == day){
                    extra.append(shift[shiftID].name);
                    return extra;
                }
            }/*
            if(info.country.compare("Colombia") == 0){
                if()
            }*/
        }
        return shift[shiftID].name;
}

int typeSchedule(string shiftName, shiftMap &shift) {
    if(shiftName.compare("LUTO") == 0)
        return -5;
    else if(shiftName.compare("AUSENTE") == 0)
        return -4;
    else if(shiftName.compare("SOLICITUD") == 0)
        return -3;
    else if(shiftName.compare("INCAPACIDAD") == 0)
        return -2;
    else if(shiftName.compare("VACACION") == 0)
        return -1;
    else if(shiftName.compare("LIBRE") == 0)
        return 0;
    else if(shiftName.compare("ELARGO") == 0)
        return 1;
    else if(shiftName.compare("ENOCHE") == 0)
        return 2;
    else
        for(shiftMap::iterator it = shift.begin(); it != shift.end(); ++it)
            if(shiftName.compare(it->second.name) == 0)
                return it->first;
}

intIntVectMap schedulerGeneretor(shiftMap &shift, int daysInMonth, int currentDay) {
    int auxDay;
    intIntVectMap dayMap;
    for(int i = 1; i <= daysInMonth; i++){
        dayMap[i].push_back(currentDay);
        for(shiftMap::iterator it = shift.begin(); it != shift.end(); it++){
            auxDay = currentDay - 1;
            if(auxDay == -1)
                auxDay = 6;
            dayMap[i].push_back(it->second.employeesRequired[auxDay]);
        }
        currentDay++;
        if(currentDay == 7)
            currentDay = 0;
    }
    return dayMap;
}

string getDirectory(string path, string limit, string replace) {
    string newPath;
    strVect splitVect;
    ifstream nothing;
    int length = split(nothing,splitVect,path.c_str(),'/');
    if(path[0] == '/')
        newPath = "/";
    for(int i = 0; i < length; i++){
        if(!limit.empty()){
            if(splitVect[i].compare(limit) == 0){
                if(!replace.empty())
                    multipleAppend(newPath,{replace,"/"});
                break;
            }
            multipleAppend(newPath,{splitVect[i],"/"});
        }
    }
    return newPath; 
}

void multipleAppend(string &filename, strVect toAppend) {
    int vectLength = toAppend.size();
    for(int i = 0; i < vectLength; i++)
        filename.append(toAppend[i]);
}

void scheduler(int day, int month, int year, int hour, int minute, string filepath, shiftMap &shift, employeeMap &employees) {
    int daysInMonth, currentDay;
    intIntVectMap dayMap, personalSchedule, previousSchedule;
    currentDay = firstDayOfMonth(month,year);
    daysInMonth = daysInCurrentMonth(month,year);
    dayMap = schedulerGeneretor(shift,daysInMonth,currentDay);
    /*for(employeeMap::iterator it = employees.begin();it != employees.end();++it){
        cout << it->first << endl;
    }*/
    /*for(intIntVectMap::iterator it = dayMap.begin(); it != dayMap.end(); it++){
        int x = it->second.size();
        cout << it->first << "\t";
        cout << dayOfWeek(it->second[0]) << "\t";
        for(int i = 1; i < x; i++)
            cout << it->second[i] << "\t";
        cout << endl;
    }*/
    if(info.country.compare("chile")){
        for(employeeMap::iterator it = employees.begin(); it != employees.end(); ++it){
            if(it->second.pregnant == 1){
                it->second.shiftType = 1;
                it->second.priority = 9;
            }
        }  
    }
    readPreviousSchedules(month,year,filepath,previousSchedule,shift,employees);
    personalSchedule = assingShift(employees,shift,dayMap,previousSchedule,daysInMonth,month);
    /*for(intIntVectMap::iterator it = personalSchedule.begin(); it != personalSchedule.end(); it++){
        int x = it->second.size();
        cout << it->first << "\t" << "\t\tHorario:|";
        for(int i = 0; i < x; i++)
            cout << it->second[i] << "|";
        cout<<endl;
    }*/
    /*for(intIntVectMap::iterator it = dayMap.begin(); it != dayMap.end(); it++){
        cout << it->first << "\t";
        cout << dayOfWeek(it->second[0]) << "\t";
        cout << it->second[1] << "\t" << it->second[2] << "\t" << it->second[3] << endl;
    }*/
    filepath.append("output/");
    mkdir(filepath.c_str(),S_IRWXU | S_IRWXG | S_IROTH | S_IXOTH);
    multipleAppend(filepath,{currentTime(hour,minute),"/"});
    mkdir(filepath.c_str(),S_IRWXU | S_IRWXG | S_IROTH | S_IXOTH);
    personalPrinter(filepath,personalSchedule,employees,dayMap[1][0],dayMap.size(),shift);
    weeklyPersonalPrinter(filepath,employees,shift,personalSchedule,dayMap);
    multipleAppend(filepath,{currentMonth(month),to_string(year)});
    monthlyPrinter(filepath,employees,personalSchedule,dayMap,shift);
    weeklyPrinter(filepath,employees,shift,personalSchedule,dayMap);
}

bool otherShifts(int shiftID, int shiftType, shiftMap &shift, int day, intIntVectMap &dayMap, int daysInMonth) {
    int vectLength, auxDay;
    bool flag = false;
    if(shift.count(shiftType) == 0 && shiftType > 0 && info.shiftCombinations.size() != 0){
        if(daysInMonth != day){
            vectLength = info.shiftCombinations[shiftType].size();
            for(int i = 1; i < vectLength; i++){
                if(info.shiftCombinations[shiftType][i] != 0 && dayMap[day+1][info.shiftCombinations[shiftType][i]] == 0 && shiftID != info.shiftCombinations[shiftType][i])
                    return false;
            }
        }
        vectLength = info.shiftCombinations[shiftType].size();
        for(int i = 0; i < vectLength; i++)
            if(info.shiftCombinations[shiftType][i] == shiftID)
                return true;
        return false;
    }
    return true;
}

bool canExploit(int explotedPriority, int dayNumber, int explotedID , int employeeID, intIntVectMap &personalSchedule, employeeMap &employees) {
    if(employees[employeeID].pregnant == 1)
        return false;
    if(explotedID != 0){
        if(employees[employeeID].weekExtraWorked > employees[explotedID].weekExtraWorked || employees[employeeID].hours > employees[explotedID].hours){
            return false;
        }
    }
    if(explotedPriority >= employees[employeeID].priority && employees[employeeID].extraWork == 0){
        if(dayNumber < 2){
            if(dayNumber > personalSchedule[employeeID].size())
                return true;
            if(personalSchedule[employeeID][dayNumber-1] == 0)
                return true;
        }
        if(dayNumber > personalSchedule[employeeID].size()){
            if(personalSchedule[employeeID][dayNumber-2] == 0 || explotedID == 0)
                return true;
            if(personalSchedule[explotedID][dayNumber-2] == 0)
                return false;
            return true;
        }else if(personalSchedule[employeeID][dayNumber-1] == 0){
            if(personalSchedule[employeeID][dayNumber-2] == 0 || explotedID == 0)
                return true;
            if(personalSchedule[explotedID][dayNumber-2] == 0)
                return false;
            return true;
        }
    }
    return false;
}

bool nextShift(int employeeID, int shiftType, intIntVectMap &previousSchedule, intIntVectMap &personalSchedule, intIntVectMap &dayMap, struct Employees &employeeData) {
    int auxDay, shiftToAssign = 0;
    bool flag = false;
    if(previousSchedule.count(employeeID) > 0 && info.shiftCombinations.count(shiftType) > 0){
        auxDay = 8 - info.shiftCombinations[shiftType].size();
        if(auxDay > -1){
            while(auxDay < 7){
                if(previousSchedule[employeeID][auxDay] == info.shiftCombinations[shiftType][0])
                    break;
                auxDay += 1;
            }
            for(int i = auxDay; i < 7; i++)
                shiftToAssign += 1;
            auxDay = info.shiftCombinations[shiftType].size();
            for(int i = shiftToAssign, day = 1; i < auxDay; i++, day++){
                int nextShiftToAssign = info.shiftCombinations[shiftType][i];
                if(day > 1 && flag == false){               //Analiza disponibilidad laboral para los días posteriores
                    if(checkMyBallsForLungCancer(employeeData,personalSchedule,day,employeeID,true) > 0 && nextShiftToAssign > 0)
                        flag = true;
                }
                if(flag == true && nextShiftToAssign == 0){     //Si se interrumpió el asignamiento y aún faltan libres por asignar, quedan pendientes.
                    employeeData.freeDaysToSchedule++;
                }else{                                      //No se encontró impedimento para asignar.
                    if(nextShiftToAssign > 0)
                        dayMap[day][nextShiftToAssign]--;
                    personalSchedule[employeeID].push_back(nextShiftToAssign);
                }
            }
            return true;
        }
    }
    return false;
}

intIntVectMap assingShift(employeeMap &employees, shiftMap &shift, intIntVectMap &dayMap, intIntVectMap &previousSchedule, int daysInMonth, int month) {
    int employeeID, assignedDays, Rep = 0, aux = 0, aux2 = 0, aux3 = 0;
    int explotedID = 0, explotedPriority = 99;
    intVect thoseGuysNeedRest;
    intIntVectMap personalSchedule;
    for(int i = 1; i <= daysInMonth; i++){
        for(shiftMap::iterator it = shift.begin(); it != shift.end(); it++){
            int shiftID = it->first;
            if(dayMap[i][0] == 0){
                for(employeeMap::iterator it = employees.begin(); it != employees.end(); it++){
                    it->second.extraWork = 0;
                }
            }
            while(dayMap[i][shiftID] > 0){
                if(aux != i || aux2 != shiftID || aux3 != dayMap[i][shiftID]){
                    aux3 = dayMap[i][shiftID];
                    aux = i;
                    aux2 = shiftID;
                    Rep = 0;
                }else{
                    Rep++;
                }
                for(employeeMap::iterator it = employees.begin(); it != employees.end(); it++){
                    intVect::iterator auxIterator = find(it->second.criticalDays.begin(),it->second.criticalDays.end(),i);
                    employeeID = it->first;
                    assignedDays = personalSchedule[employeeID].size();
                    if(Rep == 4){
                        //cout << explotedID << endl;
                        //cout << it->first << " - " << it->second.extraWork << endl;
                        if(canExploit(explotedPriority, i , explotedID, employeeID ,personalSchedule,employees) && checkMyBallsForLungCancer(it->second,personalSchedule,i,employeeID,0) == 0 ){
                            if(info.country.compare("chile") == 0){
                                if(it->second.shiftType!= 3){
                                    if(personalSchedule[employeeID][i-2] == 2 && shiftID == 1){

                                    }else{
                                        explotedID=employeeID;
                                        explotedPriority=it->second.priority;
                                    }
                                    
                                }
                            }else if(info.country.compare("colombia") == 0){
                                explotedID = employeeID;
                                explotedPriority = it->second.priority;
                            }
                            
                        }
                    }
                    //std::cout << "No puedo asignar a "<< employeeID << " el turno "<< shiftID << " el dia " << i << " con "<< dayMap[i][shiftID] << " por asignar"<< std::endl;
                    if(assignedDays >= i)
                        continue;
                    if(isWorkingDay(it->second,dayMap[i][0],i,shiftID) && assignedDays < i && otherShifts(shiftID,it->second.shiftType,shift,i,dayMap,daysInMonth) && restCondition(Rep,employeeID,thoseGuysNeedRest) && (auxIterator == it->second.criticalDays.end() || Rep == 3)) {
                        if(assignedDays == 0){                          //Si el funcionario no tiene nada asignado aun, se le asigna de inmediato el turno que este pida.
                            if(info.country.compare("chile") == 0){
                                if(it->second.shiftType != 3 && shiftID != 3){
                                    if(nextShift(it->first,it->second.shiftType,previousSchedule,personalSchedule,dayMap,employees[it->first])){
                                        Rep = 0;
                                        break;
                                    }
                                }
                                if(shift.count(it->second.shiftType) == 0 && it->second.shiftType != 0 && info.shiftCombinations.size() != 0){
                                    addShift(personalSchedule,info.shiftCombinations[it->second.shiftType],employeeID,dayMap,i,shiftID,daysInMonth,it->second);
                                    giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                    break;
                                }
                            }
                            /*if(it->second.shiftType > shift.size() && it->second.shiftType != 0 && info.shiftCombinations.size() != 0 && info.country.compare("chile") == 0){
                                addShift(personalSchedule,info.shiftCombinations[it->second.shiftType],employeeID,dayMap,i,shiftID,daysInMonth,it->second);
                                giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                break;*/
                            if(info.country.compare("colombia") == 0 && (dayMap[i][0] == 6 || dayMap[i][0] == 0) && shiftID != 3){
                                dayMap[i][1]--;
                                dayMap[i][2]--;
                                personalSchedule[employeeID].push_back(shiftID);
                                giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                it->second.hours += 12;
                                Rep = 0;
                                break;
                            }else{
                                personalSchedule[employeeID].push_back(shiftID);
                                giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                dayMap[i][shiftID]--;
                                it->second.hours += shift[shiftID].length;
                                Rep = 0;
                                break;
                            }
                        }
                        //cout << i <<": Funcionario:"<<employeeID <<endl;
                        //std::cout << "No puedo asignar a "<< employeeID << " el turno "<< shiftID << " el dia " << i << " con "<< dayMap[i][shiftID] << " por asignar "<<Rep <<std::endl;
                        if(canSchedule(shift[shiftID],personalSchedule,employeeID,i)){                                                                                                              //Comienza la asignacion
                            if(consecutiveDays(personalSchedule,employeeID,shiftID,shift)){
                                if(assignedDays == 1){
                                    //cout << info.country << endl;
                                    if(it->second.shiftType > shift.size() && it->second.shiftType != 0 && info.shiftCombinations.size() != 0  && info.country.compare("chile") == 0){
                                        addShift(personalSchedule,info.shiftCombinations[it->second.shiftType],employeeID,dayMap,i,shiftID,daysInMonth,it->second);
                                        giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                        Rep = 0;
                                        break;
                                    }else if(info.country.compare("colombia") == 0 && (dayMap[i][0] == 6 || dayMap[i][0] == 0) && shiftID != 3){
                                        dayMap[i][1]--;
                                        dayMap[i][2]--;
                                        personalSchedule[employeeID].push_back(shiftID);
                                        giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                        it->second.hours += 12;
                                        Rep = 0;
                                        break;
                                    }else{
                                        personalSchedule[employeeID].push_back(shiftID);
                                        dayMap[i][shiftID]--;
                                        giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                        it->second.hours += shift[shiftID].length;
                                        Rep = 0;
                                        break;
                                    }
                                }
                                //std::cout << "No puedo asignar a "<< employeeID << " el turno "<< shiftID << " el dia " << i << " con "<< dayMap[i][shiftID] << " por asignar"<< std::endl;
                                if(weekMax(personalSchedule,previousSchedule,dayMap,employeeID,i,shift)){
                                    /*if(it->second.freeDaysToSchedule > 0 && Rep == 0){
                                        //cout << it->first << " " << it->second.freeDaysToSchedule << endl;
                                        thoseGuysNeedRest.push_back(employeeID);
                                        Rep--;
                                        break;
                                    }*/
                                    //cout << dayMap[6][0];
                                    if(it->second.shiftType > shift.size() && it->second.shiftType != 0 && info.shiftCombinations.size() != 0  && info.country.compare("chile") == 0){
                                        addShift(personalSchedule,info.shiftCombinations[it->second.shiftType],employeeID,dayMap,i,shiftID,daysInMonth,it->second);
                                        giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                        Rep = 0;
                                        break;
                                    }else if(info.country.compare("colombia") == 0 && (dayMap[i][0] == 6 || dayMap[i][0] == 0) && shiftID != 3){
                                        dayMap[i][1]--;
                                        dayMap[i][2]--;
                                        personalSchedule[employeeID].push_back(shiftID);
                                        giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                        employees[employeeID].LargeColombia.push_back(i-1);
                                        it->second.hours += 12;
                                        Rep = 0;
                                        break;
                                    }else{
                                        personalSchedule[employeeID].push_back(shiftID);
                                        dayMap[i][shiftID]--;
                                        giveMeMyFreeDays(it->second.freeDaysToSchedule,it->second.freeDaysCounter,i,month);
                                        it->second.hours += shift[shiftID].length;
                                        Rep = 0;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                if(info.country.compare("colombia") == 0 && (dayMap[i][0] == 6 || dayMap[i][0] == 0) && shiftID != 3 && Rep == 4 && explotedID != 0 && personalSchedule[explotedID].size() < i && weekMax(personalSchedule,previousSchedule,dayMap,employeeID,i,shift) && shiftID == 1){
                    dayMap[i][1]--;
                    dayMap[i][2]--;
                    personalSchedule[explotedID].push_back(shiftID+1);
                    giveMeMyFreeDays(employees[explotedID].freeDaysToSchedule,employees[explotedID].freeDaysCounter,i,month);
                    employees[explotedID].hours += 12;
                    employees[explotedID].LargeColombia.push_back(i-1);
                    explotedID=0;
                    break;
                }
                if(Rep == 4 && explotedID != 0 && personalSchedule[explotedID].size() < i && !info.country.compare("chile")){
                    //cout << explotedID << endl;
                    employees[explotedID].extraWork = 1;
                    employees[explotedID].freeDaysToSchedule++;
                    personalSchedule[explotedID].push_back(shiftID);
                    employees[explotedID].extraDays.push_back(i-1);
                    dayMap[i][shiftID]--;
                    employees[explotedID].weekExtraWorked++;
                    explotedPriority = 99;
                    explotedID = 0;
                }else if(Rep == 4 && explotedID != 0 && personalSchedule[explotedID].size() >= i && !info.country.compare("chile")){
                    //cout << explotedID << endl;
                    employees[explotedID].extraWork = 1;
                    employees[explotedID].freeDaysToSchedule++;
                    personalSchedule[explotedID][i-1] = shiftID;
                    employees[explotedID].extraDays.push_back(i-1);
                    employees[explotedID].weekExtraWorked++;
                    dayMap[i][shiftID]--;
                    explotedPriority = 99;
                    explotedID = 0;
                }
                if(Rep == 5 && explotedID == 0){
                    Volantes[i].push_back(shiftID);
                    dayMap[i][shiftID]--;
                    /*cout << "Error: Personal insuficiente para el dia " << i << ", en el turno "<< shiftID <<endl << endl;
                    exit(EXIT_FAILURE);*/
                }
            }
        }
        /*if(thoseGuysNeedRest.size() != 0){
            //cout << "entre" << endl;
            while(!thoseGuysNeedRest.empty()){
                if(personalSchedule[thoseGuysNeedRest.back()].size()<i){
                    personalSchedule[thoseGuysNeedRest.back()].push_back(0);
                    employees[thoseGuysNeedRest.back()].freeDaysToSchedule--;
                }
                thoseGuysNeedRest.pop_back();
            }
        }*/
        for(employeeMap::iterator it = employees.begin(); it != employees.end(); it++){
            employeeID = it->first;
            assignedDays = personalSchedule[employeeID].size();
            if(assignedDays < i)
                if(checkMyBallsForLungCancer(it->second,personalSchedule,i,it->first) == 0)
                    personalSchedule[employeeID].push_back(0);
        }
    }
    return personalSchedule;
}

bool restCondition(int Rep, int employeeID, intVect &thoseGuysNeedRest) {
    int size = thoseGuysNeedRest.size();
    if(size == 0 || Rep > 1)
        return true;
    for(int i = 0; i < size ;i++){
        if(employeeID == thoseGuysNeedRest[i])
            return false;
    }
    return true;
}

void giveMeMyFreeDays(int &freeDaysToSchedule,int &freeDaysCounter,int day,int month) {
    int vectLength = info.totalHolidays[month].size();
    for(int i = 0; i < vectLength;i++){
        if(day == info.totalHolidays[month][i]){
            freeDaysCounter++;
            freeDaysToSchedule++;
        }
    }
}

int checkMyBallsForLungCancer(struct Employees &employeeData, intIntVectMap &personalSchedule, int currentDay, int employeeID, bool flag) {
    int vectLength;                                                                                                     //El booleano flag verifica si se desea añadir libres con esta funcion o no.
    if((vectLength = employeeData.disability.size()) != 0){                                                             //Verifica si el empleado tiene alguna licencia medica por revisar
        for(int i = 0; i < vectLength; i += 2){
            if(employeeData.disability[i] == 0 && employeeData.disability[i+1] >= currentDay){          
                if(flag == true)
                    personalSchedule[employeeID].push_back(-2);
                return 4;
            }else if(employeeData.disability[i+1] == 0 && employeeData.disability[i] <= currentDay){                    
                if(flag == true)
                    personalSchedule[employeeID].push_back(-2);
                return 4;
            }else if(employeeData.disability[i+1] >= currentDay && employeeData.disability[i] <= currentDay){
                if(flag == true)
                    personalSchedule[employeeID].push_back(-2);
                return 4;
            }
        }
    }
    if((vectLength = employeeData.deceased.size()) != 0){                                                               //Verifica si el empleado tiene algun luto por asignar
        for(int i = 0; i < vectLength; i += 2){
            if(employeeData.deceased[i] == 0 && employeeData.deceased[i+1] >= currentDay){
                if(flag == true)
                    personalSchedule[employeeID].push_back(-5);
                return 3;
            }else if(employeeData.deceased[i] <= currentDay && employeeData.deceased[i+1] == 0){
                if(flag == true)
                    personalSchedule[employeeID].push_back(-5);
                return 3;
            }else if(employeeData.deceased[i] <= currentDay && employeeData.deceased[i+1] >= currentDay){
                if(flag == true)
                    personalSchedule[employeeID].push_back(-5);
                return 3;
            }
        }
    }
    if(employeeData.holidays[0] != -1){                                                                                 //Verifica si el empleado tiene vacaciones durante este periodo
        if(employeeData.holidays[0] == 0 && employeeData.holidays[1] >= currentDay){
            if(flag == true)
                personalSchedule[employeeID].push_back(-1);
            return 2;
        }else if(employeeData.holidays[1] == 0 && employeeData.holidays[0] <= currentDay){
            if(flag == true)
                personalSchedule[employeeID].push_back(-1);
            return 2;
        }else if(employeeData.holidays[0] <= currentDay && employeeData.holidays[1] >= currentDay){
            if(flag == true)
                personalSchedule[employeeID].push_back(-1);
            return 2;
        }
    }
    if((vectLength = employeeData.freeDays.size()) != 0){                                                               //Verifica si el empleado tiene dias libres solicitados durante este periodo
        for(int i = 0; i < vectLength; i++){
            if(employeeData.freeDays[i] == currentDay){
                if(flag == true){
                    employeeData.freeDaysToSchedule--;
                    personalSchedule[employeeID].push_back(-3);
                }
                return 1;
            }
        }
    }
    return 0;
}

void addShift(intIntVectMap &personalSchedule, intVect &shift, int employeeID, intIntVectMap &dayMap, int day, int shiftID, int daysInMonth, struct Employees &employeeData) {
    int aux = day, vectLength;
    for(int i = 0; i< shift.size(); i++){                                                                   //Funcion para añadir tercer o cuarto turno, los turnos se añaden uno a uno, para evitar
        if(shiftID == shift[i+1])                                                                           //que se le asigne un dia que no deberia trabajar o si ya se alcanzo el fin de mes.
            i++;
        personalSchedule[employeeID].push_back(shift[i]);
        employeeData.hours += 12;
        if(shift[i] != 0)
            dayMap[aux][shift[i]]--;
        aux++;
        if(aux > daysInMonth || checkMyBallsForLungCancer(employeeData,personalSchedule,aux,employeeID))
            return;
    }
}

bool consecutiveDays(intIntVectMap &personalSchedule, int employeeID, int shiftID,shiftMap &shift) {
    int count = 0, aux;
    if(shiftID == personalSchedule[employeeID][personalSchedule[employeeID].size()-1] ){                    //Verifica si estoy asignando un turno igual al que ya se asigno el dia anterior
        for(int i = personalSchedule[employeeID].size() - 1; i >= 0; i--){                                  //En este caso se revisa la cantidad de veces que trabaja el mismo turno
            if(personalSchedule[employeeID][i] == shiftID)                                                  //en caso de que supere el numero maximo de veces consecutivas que puede tomar este turno se retorna falso
                count++;                                                                                    //en el caso que aun no se llegue a la cantidad maxima consecutiva retorna verdadero
            if(count == shift[shiftID].maxConsecutive)
                return false;
            if(personalSchedule[employeeID][i] <= 0)
                break;
        }
        return true;
    }else{
        for(int i = personalSchedule[employeeID].size() - 1 ; i >= 0; i--){
            if(personalSchedule[employeeID][i] != 0)
                count++;
            if(personalSchedule[employeeID][i] <= 0)
                break;
        }
        if(count == 2){
            return false;
        }
        return true;
    }
}


bool weekMax(intIntVectMap &personalSchedule, intIntVectMap &previousSchedule, intIntVectMap &dayMap, int employeeID, int dayNumber, shiftMap &shift) {
    int aux = dayNumber - 1, aux2, horas = 0;
    if(dayMap[dayNumber][0] <= 0)
        return true;
    while(dayMap[aux][0] != 0){
        if(aux == 1){
            if(!previousSchedule.empty()){
                for(int i = 6, aux2 = dayMap[1][0] - 1; aux2 >= 0; i--, aux2--){
                    if(previousSchedule[employeeID][i] > 0)
                        horas += shift[previousSchedule[employeeID][i]].length;
                }
            }
            break;
        }
        aux--;
    }
    //cout<<dayNumber <<" DIA "<< aux << endl;
    if(aux == dayNumber - 1)
        return true;
    for(int i = aux; i < dayNumber; i++){
        if(personalSchedule[employeeID][i-1] > 0)
            horas += shift[personalSchedule[employeeID][i-1]].length;
    }
    //cout << horas << endl;
    if(info.maxHoursPerWeek <= horas)
        return false;
    else
        return true;
}

bool canSchedule(struct Settings &shiftData, intIntVectMap &personalSchedule, int employeeID, int dayNumber) {
    int vectLength = shiftData.invalidNextShift.size();
    for(int i = 0; i < vectLength; i++)
        if(personalSchedule[employeeID][dayNumber-2] == shiftData.invalidNextShift[i])
            return false;
    return true;
}

bool isWorkingDay(struct Employees &employeeData, int day, int dayNumber, int shiftID) {
    intIntVectMap notUsable;
    day--;
    if(employeeData.blocked[0] != -1){
        if((employeeData.blocked[0] == 0 || employeeData.blocked[0] <= dayNumber) && (employeeData.blocked[1] >= dayNumber || employeeData.blocked[1] == 0))
            return false;
    }
    if(day == -1)
        day = 6;
    if(shiftID == 3 && employeeData.shiftType == 0 && info.country.compare("chile") == 0)
        return false;
    if(employeeData.workingDays[day] && (employeeData.shiftType == shiftID || employeeData.shiftType == 0 || info.shiftCombinations.count(employeeData.shiftType) != 0)) {
        if(checkMyBallsForLungCancer(employeeData,notUsable,dayNumber,1,false) == 0)
            return true;
    }
    return false;
}

//Lee el archivo con la configuración para un servicio dado.
void readSettings(const string &filename, shiftMap &shift) {
    int id, totalShifts = 0;
    string buffer;
    ifstream input(filename.c_str());
    if(input.is_open()){
        moveFilePointer(input,2,0,"# Dia de la semana");
        for(int i = 0; !(buffer = moveFilePointer(input,1)).empty(); i++){
            strVect splitVect;
            split(input,splitVect,buffer.c_str(),'=',2,2);
            if(i == 0){
                info.country = splitVect[1];
                transform(info.country.begin(),info.country.end(),info.country.begin(),::tolower);
            }else if(i == 1){
                info.maxHoursPerDay = formatErrorHandler(input,splitVect[1].c_str(),1,24);
            }else if(i == 2){
                info.maxHoursPerWeek = formatErrorHandler(input,splitVect[1],1,168);
            }else{
                cout << endl << "Error en definición de variables globales. Existe un número diferente de líneas al esperado (3)" << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
        }
        moveFilePointer(input,2,11,"Nombre");
        while(!(buffer = moveFilePointer(input,1)).empty()){
            strVect splitVect;
            split(input,splitVect,buffer.c_str(),',',4,4);
            id = formatErrorHandler(input,splitVect[0]);
            if(shift.count(id) > 0){
                cout << endl << "Error(TIPOS_DE_TURNOS): La ID de turno "<< id << " está repetida" << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            shift[id].name = splitVect[1];
            shift[id].length = formatErrorHandler(input,splitVect[2],1,12);
            shift[id].maxConsecutive = formatErrorHandler(input,splitVect[3],1,7);
            totalShifts++;
        }
        moveFilePointer(input,2,11,"TurnoID(s)");
        while(!(buffer = moveFilePointer(input,1)).empty()){
            strVect splitVect;
            int vectLength = split(input,splitVect,buffer.c_str(),',',1,totalShifts+1);
            id = formatErrorHandler(input,splitVect[0]);
            if(shift.count(id) == 0){
                cout << endl << "Error(TURNOS_ANTERIORES_INVALIDOS): El turno " << id << " no existe" << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            for(int i = 1; i < vectLength; i++){
                int x = formatErrorHandler(input,splitVect[i]);
                if(shift.count(x) > 0 && splitVect[0] != splitVect[i]){
                    shift[id].invalidNextShift.push_back(x);
                }else{
                    cout << endl << "Error(TURNOS_ANTERIORES_INVALIDOS): El turno " << id << " no existe" << endl;
                    input.close();
                    exit(EXIT_FAILURE);
                }
            }
        }
        moveFilePointer(input,2,26,"separados");
        while(!(buffer = moveFilePointer(input,1)).empty()){
            strVect splitVect;
            int vectLength = split(input,splitVect,buffer.c_str(),',',4,100);
            id = formatErrorHandler(input,splitVect[0]);
            if(shift.count(id) > 0){
                cout << endl << "Error(COMBINACIONES_VALIDAS_DE_TURNOS): El turno " << id << " ya existe" << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            for(int i = 1; i < vectLength; i++){
                int x = formatErrorHandler(input,splitVect[i],0,totalShifts);
                if(shift.count(x) > 0 || x == 0){
                    info.shiftCombinations[id].push_back(x);
                }else{
                    cout << endl << "Error(COMBINACIONES_VALIDAS_DE_TURNOS): El turno " << x << " no existe" << endl;
                    input.close();
                    exit(EXIT_FAILURE);
                }
            }
        }
        moveFilePointer(input,2,11,"Cantidad");
        while(!(buffer = moveFilePointer(input,1)).empty()){
            strVect splitVect;
            split(input,splitVect,buffer.c_str(),',',8,8);
            id = formatErrorHandler(input,splitVect[0]);
            if(shift.count(id) == 0){
                cout << endl << "Error(REQUISITOS_DE_TURNOS_POR_DIA): El turno " << id << " no existe" << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            for(int i = 0; i < 7; i++)
                shift[id].employeesRequired[i] = formatErrorHandler(input,splitVect[i+1],0,30);
        }
    }else{
        cout << endl << "Error en apertura del archivo '" << filename << "'. Terminando ejecucion..." << endl;
        exit(EXIT_FAILURE);
    }
    input.close();
}

//Lee los datos de cada funcionario para un servicio dado.
void readEmployees(int month, int year, const string &filename, employeeMap &employee, shiftMap &shift) {
    ifstream input(filename.c_str());
    string buffer;
    int id, daysInMonth = daysInCurrentMonth(month,year);
    if(input.is_open()){
        moveFilePointer(input,7,3,"Nombre");
        while(!(buffer = moveFilePointer(input,1)).empty()){        //Lee la primera sección del archivo, que corresponde
            int x;
            strVect splitVect;                                      //a la ID, nombre y preferencias de cada funcionario
            split(input,splitVect,buffer.c_str(),';',12,12);
            id = formatErrorHandler(input,splitVect[0]);                  //FuncionarioID
            if(employee.count(id) > 0){                               //Se chequea que no se agregue ID repetida
                cout << endl << "Error(LISTADO_DE_FUNCIONARIOS): El funcionario "<< id <<" está repetido" << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            employee[id].name = splitVect[1];                       //Nombre funcionario
            employee[id].lastname = splitVect[2];
            for(int i = 3; i < 10; i++)                              //Días de la semana que el funcionario trabaja.
                employee[id].workingDays[i-3] = formatErrorHandler(input,splitVect[i],0,1);
            x = formatErrorHandler(input,splitVect[10]);
            if(shift.count(x) > 0 || x == 0 || info.shiftCombinations.count(x) != 0){
                employee[id].shiftType = x;
            }else{
                cout << endl << "Error(LISTADO_DE_FUNCIONARIOS): Turno preferido de funcionario " << id << " no existe" << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            if(employee[id].shiftType == 3 && info.country.compare("chile") == 0)
                employee[id].priority = 10;
            else
                employee[id].priority = formatErrorHandler(input,splitVect[11],1,10);
        }
        moveFilePointer(input,2,17,"Dia");
        while(!(buffer = moveFilePointer(input,1)).empty()){        //Sección correspondiente a días libres por solicitud.
            strVect splitVect;
            int vectLength = split(input,splitVect,buffer.c_str(),';',2,daysInMonth);
            id = formatErrorHandler(input,splitVect[0]);
            if(employee.count(id) == 0){                              //Se chequea que FuncionarioID exista previamente.
                cout << endl << "Error(DIAS_LIBRES_POR_SOLICITUD_APROBADA): El funcionario " << id << " no existe." << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            for(int i = 1; i < vectLength; i++)                     //Se agregan los días al vector de la estrucutura.
                employee[id].freeDays.push_back(formatErrorHandler(input,splitVect[i],1,daysInMonth));
        }
        moveFilePointer(input,2,17,"Dia");
        while(!(buffer = moveFilePointer(input,1)).empty()){
            strVect splitVect;
            split(input,splitVect,buffer.c_str(),';',3,3);
            id = formatErrorHandler(input,splitVect[0]);
            if(employee.count(id) == 0){
                cout << endl << "Error(DIAS_DE_VACACIONES_POR_FUNCIONARIO): El funcionario " << id << " no existe." << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            for(int i = 1; i < 3; i++)
                employee[id].holidays[i-1] = formatErrorHandler(input,splitVect[i],0,daysInMonth);
        }
        moveFilePointer(input,2,17,"Dia");
        while(!(buffer = moveFilePointer(input,1)).empty()){
            strVect splitVect;
            split(input,splitVect,buffer.c_str(),';',3,3);
            id = formatErrorHandler(input,splitVect[0]);
            if(employee.count(id) == 0){
                cout << endl << "Error(DIAS_LIBRES_POR_RAZONES_MEDICAS): El funcionario " << id << " no existe." << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            for(int i = 1; i < 3; i++)
                employee[id].disability.push_back(formatErrorHandler(input,splitVect[i],0,daysInMonth));                 
        }
        moveFilePointer(input,2,17,"Dia");
        while(!(buffer = moveFilePointer(input,1)).empty()){
            strVect splitVect;
            split(input,splitVect,buffer.c_str(),';',3,3);
            id = formatErrorHandler(input,splitVect[0]);
            if(employee.count(id) == 0){
                cout << endl << "Error(DIAS_LIBRES_POR_FALLECIMIENTO_DE_FAMILIAR): El funcionario " << id << " no existe." << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            for(int i = 1; i < 3; i++)
                employee[id].deceased.push_back(formatErrorHandler(input,splitVect[i],0,daysInMonth));
        }
        moveFilePointer(input,2,17,"Dia");
        while(!(buffer = moveFilePointer(input,1)).empty()){
            strVect splitVect;
            split(input,splitVect,buffer.c_str(),';',3,3);
            id = formatErrorHandler(input,splitVect[0]);
            if(employee.count(id) == 0){
                cout << endl << "Error(PERIODO_DE_BLOQUEO_DE_FUNCIONARIO): El funcionario " << id << " no existe." << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            for(int i = 1; i < 3; i++)
                employee[id].blocked[i-1] = formatErrorHandler(input,splitVect[i],0,daysInMonth);
        }
        moveFilePointer(input,2,19,"aquella");
        while(!(buffer = moveFilePointer(input,1)).empty()){
            strVect splitVect;
            split(input,splitVect,buffer.c_str(),';',1,1);
            id = formatErrorHandler(input,splitVect[0]);
            if(employee.count(id) == 0){
                cout << endl << "Error(FUNCIONARIA_EMBARAZADA): La funcionaria " << id << " no existe." << endl;
                input.close();
                exit(EXIT_FAILURE);
            }
            employee[id].pregnant = 1;
        }
    }else{
        cout << endl << "Error en apertura del archivo '" << filename << "'. Terminando ejecucion..." << endl;
        exit(EXIT_FAILURE);
    }
    input.close();
}

//Lee la programación de los últimos 7 días del mes anterior al que se desea programar.
void readPreviousSchedules(int thisMonth, int thisYear, string filepath, intIntVectMap &previousSchedule, shiftMap &shifts, employeeMap &employee) {
    int vectLength;
    string buffer, previousPeriod;
    ifstream input;
    if(thisMonth == 1){
        filepath = getDirectory(filepath,to_string(thisYear),to_string(thisYear-1));
        filepath.append("12/");
        multipleAppend(previousPeriod,{"Diciembre",to_string(thisYear-1)});
        vectLength = 39;
    }else{
        filepath = getDirectory(filepath,to_string(thisYear),to_string(thisYear));
        multipleAppend(filepath,{to_string(thisMonth-1),"/"});
        multipleAppend(previousPeriod,{currentMonth(thisMonth-1),to_string(thisYear)});
        vectLength = daysInCurrentMonth(thisMonth-1,thisYear) + 8;
    }
    if(!(filepath = getLatestPath(filepath,previousPeriod,vectLength-8)).empty()){
        input.open(filepath);
        if(input.is_open()){
            while(moveFilePointer(input,1,0,string(),true).compare("#HORARIO MENSUAL") != 0);
            moveFilePointer(input,3);
            while(!(buffer = moveFilePointer(input,1)).empty()){
                int id, auxLenght = vectLength - 5;
                strVect splitVect;
                split(input,splitVect,buffer.c_str(),';',vectLength,vectLength);
                id = atoi(splitVect[0].c_str());
                if(employee.count(id) > 0){
                    employee[id].freeDaysToSchedule = formatErrorHandler(input,splitVect[vectLength-1],-10);
                    for(int i = auxLenght - 7; i < auxLenght; i++)
                        previousSchedule[id].push_back(typeSchedule(splitVect[i],shifts));
                }
            }
            input.close();
        }
    }
}

void readCriticalMonth(int month, int year, string filepath, employeeMap &employee, shiftMap &shift) {
    int vectLength = daysInCurrentMonth(month,year) + 8, vectLength2 = info.totalHolidays[month].size();
    string previousPeriod, buffer;
    ifstream input;
    filepath = getDirectory(filepath,to_string(year),to_string(year-1));
    multipleAppend(filepath,{to_string(month),"/"});
    multipleAppend(previousPeriod,{currentMonth(month),to_string(year-1)});
    if(!(filepath = getLatestPath(filepath,previousPeriod,vectLength-8)).empty()){
        input.open(filepath);
        if(input.is_open()){
            while(moveFilePointer(input,1,0,string(),true).compare("#HORARIO MENSUAL") != 0);
            moveFilePointer(input,3);
            while(!(buffer = moveFilePointer(input,1)).empty()){
                int id;
                strVect splitVect;
                split(input,splitVect,buffer.c_str(),';',vectLength,vectLength);
                id = atoi(splitVect[0].c_str());
                if(employee.count(id) > 0){
                    for(int i = 0; i < vectLength2; i++){
                        if(typeSchedule(splitVect[info.totalHolidays[month][i]+2],shift) > 0)
                            employee[id].criticalDays.push_back(info.totalHolidays[month][i]);
                    }
                }
            }
            input.close();
        }
    }
}

void readHolidays(const string filename, int month, int year, employeeMap &employee) {
    string buffer;
    ifstream input(filename);
    if(input.is_open()){
        moveFilePointer(input,3);
        for(int i = 1; i < 13; i++){
            int daysInMonth = daysInCurrentMonth(i,year), vectLength;
            strVect splitVect;
            buffer = moveFilePointer(input,2);
            if(!buffer.empty()){
                split(input,splitVect,buffer.c_str(),',',0,daysInMonth);
                vectLength = splitVect.size();
                for(int j = 0; j < vectLength; j++){
                    int day;
                    day = formatErrorHandler(input,splitVect[j].c_str(),1,daysInMonth);
                    info.totalHolidays[i].push_back(day);
                }
                moveFilePointer(input,1);
            }
        }
    }else{
        cout << endl << "Error en apertura del archivo '" << filename << "'. Por favor revise que el archivo esté presente." << endl;
        exit(EXIT_FAILURE);
    }
    input.close();
}

string getLatestPath(string filepath, string period, int daysInMonth) {
    int highest = 0;
    struct dirent *currentDirent;
    DIR *currentDIR = NULL;
    string latestHour = string();
    if((currentDIR = opendir(filepath.c_str())) != NULL){
        while((currentDirent = readdir(currentDIR)) != NULL){
            string day = currentDirent->d_name;
            int num = 0;
            if(strToInt(day,num) && num >= 1 && num <= daysInMonth && num > highest)
                highest = num;
        }
        if(highest > 0){
            multipleAppend(filepath,{to_string(highest),"/output/"});
            if((currentDIR = opendir(filepath.c_str())) != NULL){
                highest = -1;
                while((currentDirent = readdir(currentDIR)) != NULL){
                    string hour = currentDirent->d_name;
                    int num = -1;
                    if(strToInt(hour,num) && num >= 0 && num <= 2359 && num > highest){
                        highest = num;
                        latestHour = hour;
                    }
                }
                if(!latestHour.empty()){
                    multipleAppend(filepath,{latestHour,"/",period,".csv"});
                    return filepath;
                }
            }
        }
    }
    return string();
}

//Función para convertir string a int solo si string está compuesta por caracteres válidos.
//Extraído en parte de 'https://stackoverflow.com/questions/2844817/how-do-i-check-if-a-c-string-is-an-int'.
bool strToInt(string &toInt, int &value) {
    int stringLenght = toInt.size(), i;
    string::size_type sz;
    if(toInt.empty() || ((!isdigit(toInt[0])) && (toInt[0] != '-') && (toInt[0] != '+'))){
        return false;
    }else{
        stringLenght = toInt.size();
        for(i = 1; i < stringLenght; i++)
            if(!isdigit(toInt[i]))
                break;
        if(i == stringLenght)
            value = stoi(toInt,&sz);
        else
            return false;
    }
    return true;
}

//Función para separar un string según caractér especificado. 
//Extraído de https://stackoverflow.com/questions/53849/how-do-i-tokenize-a-string-in-c
int split(ifstream &input, strVect &result, const char *str, char c, int lower, int higher) {
    int vectLength;
    string value, temp = string(str);
    const char *begin = NULL;
    do{
        begin = str;
        while(*str != c && *str)
            str++;
        if(!(value = string(begin, str)).empty() && value.compare(" ") != 0)
            result.push_back(value);
    }while(*str++ != 0);
    if((vectLength = result.size()) >= lower && vectLength <= higher){
        for(int i = 0; i < vectLength; i++){
            int stringLenght = result[i].size();
            for(int j = 0; j < stringLenght; j++){
                if(result[i][j] == ' ')
                    result[i][j] == '\b';
            }
        }
        return vectLength;
    }else{
        cout << endl << "Error: La línea '" << temp << "' contiene parámetros fuera del límite" << endl;
        input.close();
        exit(EXIT_FAILURE);
    }
}

//Mueve puntero del archivo la cantidad de líneas especificada en parámetros.
string moveFilePointer(ifstream &input, int linesToMove, int posCheck, const string &toCheck, bool flag) {
    string buffer;
    for(int i = 0; i < linesToMove; i++)
        getline(input,buffer);
    if(flag && buffer.empty() && input.eof())
        cout << endl << "Error: Se ha alcanzado el final del archivo inesperadamente." << endl;
    else if(toCheck.empty() || buffer.find(toCheck,posCheck) != string::npos)
        return (string)buffer;
    else if(input.eof())
        cout << endl << "Error: Se esperaba leer '" << toCheck << "', pero se llegó al final del archivo." << endl;
    else
        cout << endl << "Error: Se esperaba leer '" << toCheck << "', pero se leyó '" << buffer << "'" << endl;
    input.close();
    exit(EXIT_FAILURE);
}

//Función de transformación de string a int realizando chequeos de rango y validez.
int formatErrorHandler(ifstream &input, const string &buffer, int lower, int higher) {
    int num, stringLenght, i;
    string::size_type sz;
    if(buffer.empty() || ((!isdigit(buffer[0])) && (buffer[0] != '-') && (buffer[0] != '+'))){
        cout << endl << "Error: el valor ingresado no corresponde a un número." << endl;
    }else{
        stringLenght = buffer.size();
        for(i = 1; i < stringLenght; i++)
            if(!isdigit(buffer[i]))
                break;
        if(i == stringLenght){
            num = stoi(buffer,&sz);
            if(num >= lower && num <= higher)
                return num;
            else
                cout << endl << "Error: Se ingresó un valor fuera del rango establecido: [" << lower << "," << higher << "] " << endl;
        }else{
            cout << endl << "Error: El valor ingresado no corresponde a un número. " << endl;
        }
    }
    input.close();
    exit(EXIT_FAILURE);
}

int dayOfWeek(const string &weekDay) {
    if(!weekDay.compare("LUN")){
        return 1;
    }else if(!weekDay.compare("MAR")){
        return 2;
    }else if(!weekDay.compare("MIE")){
        return 3;
    }else if(!weekDay.compare("JUE")){
        return 4;
    }else if(!weekDay.compare("VIE")){
        return 5;
    }else if(!weekDay.compare("SAB")){
        return 6;
    }else if(!weekDay.compare("DOM")){
        return 0;
    }else{
        return 7;
    }
}

//Retorna el día de la semana como string.
string dayOfWeek(int weekDay) {
    if(weekDay == 1){
        return "LUN";
    }else if(weekDay == 2){
        return "MAR";
    }else if(weekDay == 3){
        return "MIE";
    }else if(weekDay == 4){
        return "JUE";
    }else if(weekDay == 5){
        return "VIE";
    }else if(weekDay == 6){
        return "SAB";
    }else if(weekDay == 0){
        return "DOM";
    }else{
        cout << endl << "Error de formato. Terminando ejecucion..." << endl;
        exit(EXIT_FAILURE);
    }
}

string currentMonth(int month) {
    if(month == 1)
        return "Enero";
    else if(month == 2)
        return "Febrero";
    else if(month == 3)
        return "Marzo";
    else if(month == 4)
        return "Abril";
    else if(month == 5)
        return "Mayo";
    else if(month == 6)
        return "Junio";
    else if(month == 7)
        return "Julio";
    else if(month == 8)
        return "Agosto";
    else if(month == 9)
        return "Septiembre";
    else if(month == 10)
        return "Octubre";
    else if(month == 11)
        return "Noviembre";
    else
        return "Diciembre";
}

string currentTime(int hour, int minute) {
    string auxHour = to_string(hour), auxMin = to_string(minute);
    if(hour == 0)
        auxHour = "00";
    else if(auxHour.size() == 1)
        auxHour.insert(0,"0");
    if(minute == 0)
        auxMin = "00";
    else if(auxMin.size() == 1)
        auxMin.insert(0,"0");
    auxHour.append(auxMin);
    return auxHour;
}

//Retorna la cantidad de días que hay en el mes actual.
int daysInCurrentMonth(int month, int year) {
    if(month==4 || month==6 || month==9 || month==11){
        return 30;
    }else if(month == 2){
        if(year % 4 == 0 && (year % 100 != 0 || year % 400 == 0))       //Año bisiesto
            return 29;
        else
            return 28;
    }else{
        return 31;
    }
}

//Retorna el día de la semana correspondiente al primer día del mes.
int firstDayOfMonth(int month, int year) {
    tm time_in = {0, 0, 0, 1, month - 1, year - 1900};
    time_t time_temp = mktime(&time_in);
    tm *time_out = localtime(&time_temp);
    return time_out->tm_wday;
}