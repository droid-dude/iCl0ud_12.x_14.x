public void Backup()
{
    string serialNumber = iOS.SerialNumber;
    string path = ".\\Backups\\" + serialNumber;
    this.p(5, "");
    this.RunSSHServer();
    try
    {
        this.SSH("mount -o rw,union,update /");
        bool flag = File.Exists(path);
        if (!flag)
        {
            Directory.CreateDirectory(path);
        }
        bool flag2 = File.Exists(".\\Backups\\" + serialNumber + ".zip");
        if (flag2)
        {
            MessageBox.Show("Sorry, Backup Already Exits!", "Informations");
            this.p(0, "");
        }
        else
        {
            Directory.CreateDirectory(Environment.CurrentDirectory + "\\Backups\\" + serialNumber + "\\FairPlay\\");
            string str = this.Exec("find /private/var/containers/Data/System/ -iname 'internal'").Replace("Library/internal", "").Replace("\n", "").Replace("//", "/");
            this.scpClient.Download(str + "Library/internal/data_ark.plist", new FileInfo(Environment.CurrentDirectory + "\\Backups\\" + serialNumber + "\\2"));
            this.p(30, "");
            this.scpClient.Download(str + "Library/activation_records/activation_record.plist", new FileInfo(Environment.CurrentDirectory + "\\Backups\\" + serialNumber + "\\1"));
            this.scpClient.Download("/private/var/wireless/Library/Preferences/com.apple.commcenter.device_specific_nobackup.plist", new FileInfo(Environment.CurrentDirectory + "\\Backups\\" + serialNumber + "\\3"));
            this.downloadFairplay();
            this.p(60, "");
            Directory.CreateDirectory(Environment.CurrentDirectory + "\\Backups\\" + serialNumber);
            string sourceDirectoryName = Environment.CurrentDirectory + "\\Backups\\" + serialNumber;
            string destinationArchiveFileName = Environment.CurrentDirectory + "\\Backups\\" + serialNumber + ".zip";
            ZipFile.CreateFromDirectory(sourceDirectoryName, destinationArchiveFileName);
            bool flag3 = Directory.Exists(Environment.CurrentDirectory + "\\Backups\\" + serialNumber);
            bool flag4 = flag3;
            if (flag4)
            {
                Directory.Delete(Environment.CurrentDirectory + "\\Backups\\" + serialNumber, true);
            }
            this.p(80, "");
            this.uploadBackup();
            this.p(100, "");
            MessageBox.Show("Your Device " + iOS.SerialNumber + " Sucessfully Backups!", "Informations", MessageBoxButtons.OK, MessageBoxIcon.Asterisk);
            HACK antihack = new HACK();
            antihack.ServerXPROHello("Backups iOS 12 - 14 Done!");
        }
    }
    catch (Exception ex)
    {
        MessageBox.Show(ex.Message, "Informations");
    }
}