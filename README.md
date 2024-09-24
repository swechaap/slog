**SLOG - Swecha Log System**

**Project Overview :**
The SLOG (Swecha Log) system is an innovative e-logging solution designed to efficiently monitor the entry (IN) and exit (OUT) flow of students and visitors at the Swecha Learning Center (SLC) of our college. This system streamlines attendance tracking by automating the process of recording student IDs via scanning and storing the data in a centralized database.

**Key Features :**

  Automated ID Scanning: The system utilizes RFID or QR code scanning to capture student IDs quickly and accurately, ensuring that every entry and exit is logged in real-time.
  
  Database Management: All scanned data is securely transmitted to a server, where it is stored in a structured database. This enables easy retrieval and management of attendance records.
  
  User Management: Administrators can easily add, remove, and update student records through a user-friendly web interface. This feature ensures that the database remains current and relevant.
  
  Customizable Reporting: The system provides robust reporting capabilities, allowing users to generate attendance reports based on various criteria such as name, department, and month. Reports can be tailored for individual students or aggregated for entire groups.
  
  Real-Time Monitoring: The system enables real-time monitoring of student attendance, providing insights into attendance patterns and helping to identify any issues related to student engagement.
  
**Technical Details :**

  Technology Stack: The project is built using a full-stack development approach, incorporating technologies such as HTML, CSS, JavaScript for the frontend, and PHP and MySQL for the backend. The system leverages IoT devices for scanning and logging attendance.
  
  User Interface: The web application is designed to be intuitive and responsive, ensuring ease of use for both students and administrators. The interface provides easy navigation for managing records and generating reports.
  
  Security Measures: The system includes authentication mechanisms to protect sensitive data and ensure that only authorized personnel can access and manage the database.
  
**Benefits :**

  Enhanced Efficiency: By automating attendance tracking, the SLOG system reduces administrative burdens and minimizes human errors associated with manual logging.
  
  Data Accessibility: The centralized database allows for quick access to attendance records, making it easier to analyze trends and prepare reports for stakeholders.
  
  Improved Engagement: By monitoring attendance, the system helps ensure that students are actively participating in the learning environment, enabling timely interventions if needed.
  
**Component List :**

1.ESP8266 NodeMCU

2.I2C LCD 16x2 Screen

3.Speaker / Buzzer

4.Fan (Optional)

5.RFID-RC522

6.HLK-PM12

7.AC Supply

**Wiring Details : **

1.ESP8266 NodeMCU Connections:

  D3 → RFID-RC522 RST
  
  D4 → RFID-RC522 SDA
  
  D5 → RFID-RC522 SCK
  
  D6 → RFID-RC522 MISO
  
  D7 → RFID-RC522 MOSI
  
  D1 → I2C LCD SCL
  
  D2 → I2C LCD SDA
  
  D8 → Piezo Speaker pin2
  
  GND → Common ground (RFID, I2C LCD, Piezo, Fan, HLK-PM12)
  
  3V3 → RFID-RC522 VCC (3.3V), I2C LCD VCC (5V)
  
  VIN → Fan 5V, HLK-PM12 +V0
  
2.I2C LCD 16x2 Connections:

  SCL → ESP8266 D1

  SDA → ESP8266 D2
  
  VCC (5V) → ESP8266 3V3
  
  GND → ESP8266 GND
  
3.Piezo Speaker Connections:

  pin1 → ESP8266 GND
  
  pin2 → ESP8266 D8
  
4.Fan Connections:

  GND → Common ground (ESP8266, HLK-PM12)
  
  5V → ESP8266 VIN, HLK-PM12 +V0
  
5.RFID-RC522 Connections:

  RST → ESP8266 D3
  
  SDA → ESP8266 D4
  
  SCK → ESP8266 D5
  
  MISO → ESP8266 D6
  
  MOSI → ESP8266 D7
  
  GND → ESP8266 GND
  
  VCC (3.3V) → ESP8266 3V3
  
6.HLK-PM12 Connections:

  +V0 → ESP8266 VIN, Fan 5V
  
  -V0 → Common ground (ESP8266, Fan)
  
  AC → AC Supply +ve and -ve
  
**Note : In this component we have totally 5 pins, 2 pins left side and 3 pins right side. Left side pins need to be connected to the AC supply . On the right side we have 3 pins , one is GND and the remaining are 5V & 12V. We are connecting 5V to the Vin of the Nodemcu.**

7.AC Supply Connections:

+ve → HLK-PM12 AC

-ve → HLK-PM12 AC

Circuit Diagram : 

![circuit_image](https://github.com/user-attachments/assets/dbabc9ab-5725-4596-a897-40a84cbc9e84)

**Steps to Set Up the Project :**

1.Prepare the Circuit:

  Ensure all components are connected correctly according to the wiring diagram.
  
2.Install Required Libraries:

  Make sure you have all necessary libraries installed in the Arduino IDE for uploading the code to the NodeMCU.
  
3.Clone the Project:

  Clone the project repository from the GitHub repository.
  
4.Set Up Project Folder:

  For Linux Users: Place the project folder (slog) in the path /var/www/html on your localhost or server.
  
  For Windows Users: Set up a local server (e.g., XAMPP, WAMP, Apache Tomcat) and place the project folder in the server's directory.
  
5.Install Server Requirements:

  Ensure you have phpMyAdmin, MySQL, and PHP installed and configured on your system.
  
6.Update Code Configuration:

  Modify the necessary details in the code, including the server IP address, file paths, and hotspot SSID and password to match your network settings.
  
**How It Works :** 

1.Network Connection:

  When the NodeMCU connects to the specified SSID, it should be on the same network as the localhost server. If using a hotspot, ensure it provides internet access.
  
2.RFID Scanning:

  When an ID card is presented to the RFID reader, the NodeMCU sends a request for the corresponding details to the server.
  
3.Data Handling:

  The sendid.php file processes the RFID data, storing it in the database and retrieving the necessary information.
  
  The getcount.php file retrieves the total count of visitors for reporting purposes.
  

![Screenshot from 2024-09-25 00-54-16](https://github.com/user-attachments/assets/70bce792-9595-4756-b8e1-e0e2ef4f6160)

![Screenshot from 2024-09-25 00-54-42](https://github.com/user-attachments/assets/483c5661-66ad-450a-8385-e37d286fd76c)

![Screenshot from 2024-09-25 00-54-52](https://github.com/user-attachments/assets/b408ec88-d9ef-416b-80d7-46e18a7733ce)

![Screenshot from 2024-09-25 00-55-07](https://github.com/user-attachments/assets/3830c233-4023-4b64-8a4b-05bb95fc1b3f)



