from google.oauth2.credentials import Credentials
from googleapiclient.discovery import build
from google.oauth2 import service_account
import pandas as pd
import logging
import sys
import os

# Set up logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.StreamHandler(sys.stdout)
    ]
)
logger = logging.getLogger(__name__)

class GoogleSheetsReader:
    def __init__(self, credentials_path):
        logger.info(f"Initializing with credentials: {credentials_path}")
        self.SCOPES = ['https://www.googleapis.com/auth/spreadsheets.readonly']
        self.credentials_path = credentials_path
        self.creds = None
        self.service = None
        self.setup_credentials()
        
    def setup_credentials(self):
        try:
            logger.info("Setting up credentials...")
            self.creds = service_account.Credentials.from_service_account_file(
                self.credentials_path, scopes=self.SCOPES)
            self.service = build('sheets', 'v4', credentials=self.creds)
            logger.info("✓ Credentials setup successful")
        except Exception as e:
            logger.error(f"❌ Failed to setup credentials: {str(e)}")
            raise
    
    def read_all_sheets(self, spreadsheet_id):
        try:
            logger.info(f"Reading all sheets from spreadsheet ID: {spreadsheet_id}")
            
            sheet = self.service.spreadsheets()
            
            # Get all sheet names
            logger.info("Getting sheet names...")
            metadata = sheet.get(spreadsheetId=spreadsheet_id).execute()
            sheets_data = {}
            
            # Get all sheets
            all_sheets = metadata.get('sheets', [])
            total_sheets = len(all_sheets)
            logger.info(f"Found {total_sheets} sheets")
            
            for idx, sheet_metadata in enumerate(all_sheets, 1):
                sheet_title = sheet_metadata['properties']['title']
                logger.info(f"Reading sheet {idx}/{total_sheets}: {sheet_title}")
                
                range_name = f"{sheet_title}!A:Z"
                try:
                    result = sheet.values().get(
                        spreadsheetId=spreadsheet_id,
                        range=range_name
                    ).execute()
                    
                    values = result.get('values', [])
                    
                    if not values:
                        logger.warning(f'No data found in sheet: {sheet_title}')
                        continue
                    
                    logger.info(f"Successfully read {len(values)} rows from {sheet_title}")
                    
                    df = pd.DataFrame(values)
                    headers = df.iloc[0]
                    df.columns = headers
                    df = df.iloc[1:]
                    df = df.reset_index(drop=True)
                    
                    logger.info(f"Sheet {sheet_title} shape: {df.shape}")
                    sheets_data[sheet_title] = df
                    
                except Exception as e:
                    logger.error(f"Error reading sheet {sheet_title}: {str(e)}")
                    continue
            
            return sheets_data
            
        except Exception as e:
            logger.error(f"Error reading spreadsheet: {str(e)}")
            logger.error(f"Error type: {type(e)}")
            return None

def generate_html_report(sheets_data):
    html = """
    <html>
    <head>
        <title>DOLE Records Report</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #2c3e50; text-align: center; }
            .sheet { margin-bottom: 30px; }
            .sheet-header { 
                background: #34495e; 
                color: white; 
                padding: 10px; 
                border-radius: 5px 5px 0 0; 
            }
            .sheet-info { 
                background: #ecf0f1; 
                padding: 10px; 
                border-left: 1px solid #bdc3c7;
                border-right: 1px solid #bdc3c7;
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-bottom: 10px;
            }
            th, td { 
                padding: 8px; 
                text-align: left; 
                border: 1px solid #bdc3c7; 
            }
            th { background: #3498db; color: white; }
            tr:nth-child(even) { background: #f9f9f9; }
        </style>
    </head>
    <body>
        <h1>DOLE SYSTEM RECORDS REPORT</h1>
    """
    
    for sheet_name, df in sheets_data.items():
        html += f"""
        <div class="sheet">
            <div class="sheet-header">
                <h2>{sheet_name}</h2>
            </div>
            <div class="sheet-info">
                <p>Rows: {len(df)} | Columns: {len(df.columns)}</p>
                <h3>Columns:</h3>
                <ul>
        """
        
        for i, col in enumerate(df.columns, 1):
            if col and str(col).lower() != 'none':
                html += f"<li>{i}. {col}</li>"
        
        html += """
                </ul>
                <h3>First 5 Rows:</h3>
            </div>
        """
        
        # Convert DataFrame to HTML table
        html += df.head().to_html(index=False, classes='data-table')
        html += "</div>"
    
    html += """
    </body>
    </html>
    """
    
    return html

if __name__ == "__main__":
    print("\n=== DOLE SYSTEM RECORDS READER ===\n")
    
    CREDENTIALS_PATH = 'decisive-octane-450603-i7-595b9960c071.json'
    logger.info(f"Starting script with credentials: {CREDENTIALS_PATH}")
    
    # Records spreadsheet ID
    RECORDS_ID = "1iE6n5eg4ihLM1JY3L_nR1tRPq3BP8i624OhrDyI4bns"
    
    try:
        reader = GoogleSheetsReader(CREDENTIALS_PATH)
        sheets_data = reader.read_all_sheets(RECORDS_ID)
        
        if sheets_data:
            # Generate HTML report
            html_report = generate_html_report(sheets_data)
            
            # Save HTML report
            with open('dole_records_report.html', 'w', encoding='utf-8') as f:
                f.write(html_report)
            
            print("\nReport generated successfully!")
            print("Open 'dole_records_report.html' in your web browser to view the data.")
            
            # Automatically open the report in the default browser
            os.system('start dole_records_report.html')
        else:
            print("No data found in any sheet")
                
    except Exception as e:
        logger.error(f"Main script error: {str(e)}") 