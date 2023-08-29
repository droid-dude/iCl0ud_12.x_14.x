public bool Activate()
{
    this.RunSSHServer();
    string serialNumber = iOS.SerialNumber;
    string path = ".\\Backups\\";
    string text = ".\\Backups\\" + serialNumber;
    string path2 = ".\\Backups\\" + serialNumber + ".zip";
    bool isConnected = this.scpClient.IsConnected;
    bool flag = File.Exists(path2);
    if (!flag)
    {
        Directory.CreateDirectory(path);
        this.downloadBackup();
    }
    bool result;
    try
    {
        this.p(5, "");
        bool flag2 = !this.sshClient.IsConnected;
        if (flag2)
        {
            this.sshClient.Connect();
        }
        this.SSH("mount -o rw,union,update /");
        bool flag3 = !this.scpClient.IsConnected;
        if (flag3)
        {
            this.scpClient.Connect();
        }
        string sourceArchiveFileName = Environment.CurrentDirectory + "\\Backups\\" + serialNumber + ".zip";
        string destinationDirectoryName = Environment.CurrentDirectory + "\\Backups\\" + serialNumber;
        ZipFile.ExtractToDirectory(sourceArchiveFileName, destinationDirectoryName);
        this.p(20, "");
        string text2 = this.Exec("find /private/var/containers/Data/System/ -iname 'internal'").Replace("Library/internal", "").Replace("\n", "").Replace("//", "/");
        this.Exec("rm -rf /var/mobile/Media/Downloads/" + serialNumber);
        this.Exec("rm -rf /var/mobile/Media/" + serialNumber);
        this.p(30, "");
        this.Exec("mkdir /var/mobile/Media/Downloads/" + serialNumber);
        this.scpClient.Upload(new DirectoryInfo(Environment.CurrentDirectory + "\\Backups\\" + serialNumber), "/var/mobile/Media/Downloads/" + serialNumber);
        this.Exec("mv -f /var/mobile/Media/Downloads/" + serialNumber + " /var/mobile/Media/" + serialNumber);
        this.Exec("chown -R mobile:mobile /var/mobile/Media/" + serialNumber);
        this.p(40, "");
        this.Exec("chmod -R 755 /var/mobile/Media/" + serialNumber);
        this.Exec("chmod 644 /var/mobile/Media/" + serialNumber + "/1");
        this.Exec("chmod 644 /var/mobile/Media/" + serialNumber + "/2");
        this.Exec("chmod 644 /var/mobile/Media/" + serialNumber + "/3");
        this.Exec("killall backboardd");
        Thread.Sleep(6000);
        this.Exec("mv -f /var/mobile/Media/" + serialNumber + "/FairPlay /var/mobile/Library/FairPlay");
        this.Exec("chmod 755 /var/mobile/Library/FairPlay");
        this.Exec("chflags nouchg " + text2 + "/Library/internal/data_ark.plist");
        this.p(50, "");
        this.Exec(string.Concat(new string[]
        {
            "mv -f /var/mobile/Media/",
            serialNumber,
            "/2 ",
            text2,
            "/Library/internal/data_ark.plist"
        }));
        this.Exec("chmod 755 " + text2 + "/Library/internal/data_ark.plist");
        this.Exec("chflags uchg " + text2 + "/Library/internal/data_ark.plist");
        this.Exec("mkdir " + text2 + "/Library/activation_records");
        this.Exec(string.Concat(new string[]
        {
            "mv -f /var/mobile/Media/",
            serialNumber,
            "/1 ",
            text2,
            "/Library/activation_records/activation_record.plist"
        }));
        this.Exec("chmod 755 " + text2 + "/Library/activation_records/activation_record.plist");
        this.Exec("chflags uchg " + text2 + "/Library/activation_records/activation_record.plist");
        this.Exec("chflags nouchg /var/wireless/Library/Preferences/com.apple.commcenter.device_specific_nobackup.plist");
        this.Exec("mv -f /var/mobile/Media/" + serialNumber + "/3 /var/wireless/Library/Preferences/com.apple.commcenter.device_specific_nobackup.plist");
        this.Exec("chown root:mobile /var/wireless/Library/Preferences/com.apple.commcenter.device_specific_nobackup.plist");
        this.Exec("chmod 755 /var/wireless/Library/Preferences/com.apple.commcenter.device_specific_nobackup.plist");
        this.Exec("chflags uchg /var/wireless/Library/Preferences/com.apple.commcenter.device_specific_nobackup.plist");
        this.Exec("launchctl unload /System/Library/LaunchDaemons/com.apple.mobileactivationd.plist");
        this.Exec("launchctl load /System/Library/LaunchDaemons/com.apple.mobileactivationd.plist");
        this.p(60, "");
        this.SSH("tar -xvf /./Files -C /./");
        this.SSH("rm /./Files");
        this.Fix();
        this.p(100, "");
        MessageBox.Show("Your Device " + iOS.SerialNumber + " Sucessfully Activated!", "Informations", MessageBoxButtons.OK, MessageBoxIcon.Asterisk);
        HACK antihack = new HACK();
        antihack.ServerXPROHello("Restore iOS 12 - 14 Done!");
        result = true;
    }
    catch (Exception ex)
    {
        MessageBox.Show(ex.Message, "Informations");
        result = false;
    }
    return result;
}