using System.Windows;
using Creamy.WPF.ViewModels;

namespace Creamy.WPF.Views
{
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
            DataContext = new MainViewModel();

            Loaded += (s, e) =>
            {
                Activate();
                Topmost = true;
                Topmost = false;
                Focus();
            };
        }
    }
}
