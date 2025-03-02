const ARDUINO_TEMPLATES = {
    'blink': {
        name: 'Мигающий светодиод',
        description: 'Простое мигание светодиода',
        components: [
            { type: 'LED', x: 300, y: 100 }
        ],
        code: `void setup() {
  pinMode(13, OUTPUT);
}

void loop() {
  digitalWrite(13, HIGH);
  delay(1000);
  digitalWrite(13, LOW);
  delay(1000);
}`,
        connections: [
            { component: 'LED_0', pin: 'GND', to: 'GND' },
            { component: 'LED_0', pin: 'VCC', to: 'D13' }
        ]
    },
    'traffic_light': {
        name: 'Светофор',
        description: 'Трехцветный светофор',
        components: [
            { type: 'LED', x: 300, y: 100 }, // Красный
            { type: 'LED', x: 300, y: 150 }, // Желтый
            { type: 'LED', x: 300, y: 200 }  // Зеленый
        ],
        code: `void setup() {
  pinMode(11, OUTPUT);
  pinMode(12, OUTPUT);
  pinMode(13, OUTPUT);
}

void loop() {
  // Красный
  digitalWrite(11, HIGH);
  digitalWrite(12, LOW);
  digitalWrite(13, LOW);
  delay(5000);
  
  // Желтый
  digitalWrite(11, LOW);
  digitalWrite(12, HIGH);
  digitalWrite(13, LOW);
  delay(2000);
  
  // Зеленый
  digitalWrite(11, LOW);
  digitalWrite(12, LOW);
  digitalWrite(13, HIGH);
  delay(5000);
}`,
        connections: [
            { component: 'LED_0', pin: 'GND', to: 'GND' },
            { component: 'LED_0', pin: 'VCC', to: 'D11' },
            { component: 'LED_1', pin: 'GND', to: 'GND' },
            { component: 'LED_1', pin: 'VCC', to: 'D12' },
            { component: 'LED_2', pin: 'GND', to: 'GND' },
            { component: 'LED_2', pin: 'VCC', to: 'D13' }
        ]
    },
    'lcd_hello': {
        name: 'Приветствие на LCD',
        description: 'Вывод приветственного сообщения на LCD дисплей',
        components: [
            { type: 'LCD', x: 300, y: 150 }
        ],
        code: `#include <LiquidCrystal_I2C.h>

LiquidCrystal_I2C lcd(0x27, 16, 2);

void setup() {
  lcd.init();
  lcd.backlight();
  lcd.setCursor(0, 0);
  lcd.print("Hello, World!");
  lcd.setCursor(0, 1);
  lcd.print("Arduino IDE Online");
}

void loop() {
  // Ничего не делаем в цикле
}`,
        connections: [
            { component: 'LCD', pin: 'GND', to: 'GND' },
            { component: 'LCD', pin: 'VCC', to: '5V' },
            { component: 'LCD', pin: 'SDA', to: 'A4' },
            { component: 'LCD', pin: 'SCL', to: 'A5' }
        ]
    },
    'button_led': {
        name: 'Кнопка и светодиод',
        description: 'Управление светодиодом с помощью кнопки',
        components: [
            { type: 'LED', x: 300, y: 100 },
            { type: 'Button', x: 300, y: 200 }
        ],
        code: `void setup() {
  pinMode(LED_0, OUTPUT);
  pinMode(Button_0, INPUT_PULLUP);
}

void loop() {
  if (digitalRead(Button_0) == LOW) {
    digitalWrite(LED_0, HIGH);
  } else {
    digitalWrite(LED_0, LOW);
  }
}`,
        connections: [
            { component: 'LED', pin: 'GND', to: 'GND' },
            { component: 'LED', pin: 'VCC', to: 'D13' },
            { component: 'Button', pin: 'GND', to: 'GND' },
            { component: 'Button', pin: 'VCC', to: 'D2' }
        ]
    }
}; 