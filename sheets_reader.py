from google.oauth2.credentials import Credentials
from googleapiclient.discovery import build
from google.oauth2 import service_account
import pandas as pd

class GoogleSheetsReader:
    def __init__(self, credentials_path):
        self.SCOPES = ["https://www.googleapis.com/auth/spreadsheets.readonly"]
        self.credentials_path = credentials_path
        self.creds = None
        self.service = None
        self.setup_credentials()
        
    def setup_credentials(self):
        self.creds = service_account.Credentials.from_service_account_file(
            self.credentials_path, scopes=self.SCOPES)
        self.service = build("sheets", "v4", credentials=self.creds)
    
    def read_sheet(self, spreadsheet_id, range_name):
        try:
            sheet = self.service.spreadsheets()
            result = sheet.values().get(
                spreadsheetId=spreadsheet_id,
                range=range_name
            ).execute()
            
            values = result.get("values", [])
            
            if not values:
                print("No data found.")
                return None
            
            df = pd.DataFrame(values)
            headers = df.iloc[0]
            df = df[1:]
            df.columns = headers
            df = df.reset_index(drop=True)
            return df
            
        except Exception as e:
            print(f"Error: {str(e)}")
            return None

if __name__ == "__main__":
    print("\n=== DOLE SYSTEM SPREADSHEET READER ===\n")
    
    CREDENTIALS_PATH = "decisive-octane-450603-i7-595b9960c071.json"
    
    SHEETS = {
        "RECORDS": {
            "id": "1iE6n5eg4ihLM1JY3L_nR1tRPq3BP8i624OhrDyI4bns",
            "range": "Records!A:Z"
        },
        "BUDGET": {
            "id": "1LA-pNvhK5wnESaPJqXCM683nlO5ua8KU31yFcWlSUZU",
            "range": "Budget!A:Z"
        },
        "ACCOUNTING": {
            "id": "1QARkoYbrj2xdqhqdpdsJK8ZZEPzDcrCUObuIoRe11Gg",
            "range": "Accounting!A:Z"
        },
        "CASHIER": {
            "id": "1zux2bW2A7_nLNugv_7uN96GQo4DHC6rxAUs3X6RwRvI",
            "range": "Cashier!A:Z"
        }
    }
    
    try:
        reader = GoogleSheetsReader(CREDENTIALS_PATH)
        
        for sheet_name, sheet_info in SHEETS.items():
            print(f"\n{"="*50}")
            print(f"Reading {sheet_name} Sheet")
            print(f"{"="*50}")
            
            df = reader.read_sheet(sheet_info["id"], sheet_info["range"])
            
            if df is not None:
                print(f"\n{sheet_name} SHEET DETAILS:")
                print(f"Number of rows: {len(df)}")
                print(f"Number of columns: {len(df.columns)}")
                print(f"\n{sheet_name} COLUMNS:")
                print(list(df.columns))
                print(f"\n{sheet_name} FIRST 5 ROWS:")
                print(df.head())
                print("\n")
            else:
                print(f"No data found in {sheet_name} sheet")
                
    except Exception as e:
        print(f"An error occurred: {e}")
