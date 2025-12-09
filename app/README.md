# Neuffer Developers Test

This repository contains my solution to the **Neuffer Developers Test**, implemented in **PHP 8.4** without any external dependencies.

---

## 🚀 Requirements

* **PHP 8.4** or higher
* No third-party libraries required

---

## 📦 Installation

1. Clone or download this repository.
2. No further setup is needed — the project uses a simple built-in autoloader.

---

## ▶️ Usage

Run the application from the command line:

```bash
php console.php --action {action} --file {file}
```

### **Available Parameters**

| Parameter  | Alias | Description                                  |
| ---------- | ----- | -------------------------------------------- |
| `--action` | `-a`  | The mathematical operation to perform        |
| `--file`   | `-f`  | Path to the CSV file containing number pairs |
| `--help`   | `-h`  | Display the help menu                        |

### **Supported Actions**

* `plus` – Addition
* `minus` – Subtraction (first - second)
* `multiply` – Multiplication
* `division` – Division (first / second)

---

## 📥 Input Format

The input CSV file must contain **two integers** per line, separated by semicolons:

```
10;20
-30;15
45;-5
```

Rules:

* Values must be integers between **-100 and 100**
* Each line must contain exactly **two** numbers

---

## 📤 Output Files

The application generates two output files:

### **1. `result.csv`**

Contains all **valid results** (greater than 0):

```
first_number;second_number;result
```

Only positive results are included.

### **2. `log.txt`**

Contains operation logs, including:

* Timestamps for operation start and end
* Entries for invalid results (≤ 0)
* Division-by-zero warnings

---

## 🧪 Testing

Run the tests via CLI:

```bash
php tests/CalculatorTest.php
```
